<?php

namespace Src\Services;

use ITAccfarm\ResellerSDK\ResellerSDK;
use Src\Traits\Singleton;

class Buy
{
    use Singleton;

    /**
     * @var array
     */
    public $statusActions = [
        'pending' => 'woocommerce_order_status_pending',
        'processing' => 'woocommerce_order_status_processing',
        'on-hold' => 'woocommerce_order_status_on-hold',
        'completed' => 'woocommerce_order_status_completed',
        'cancelled' => 'woocommerce_order_status_cancelled',
        'refunded' => 'woocommerce_order_status_refunded',
        'failed' => 'woocommerce_order_status_failed',
    ];

    /**
     * @var ResellerSDK
     */
    private $accfarmApi;

    public function __construct()
    {
        $this->accfarmApi = Accfarm::instance()->api();
    }

    public function register()
    {
        $buyStatus = get_option('accfarm_reseller_hooks_order_status_to_buy_af', 'processing');

        add_action($this->statusActions[$buyStatus], [$this, 'buy']);
    }

    /**
     *  On order status to buy
     */
    public function buy($order_id)
    {
        $order = wc_get_order($order_id);

        if (!Accfarm::instance()->authenticate()) {
            $this->sendErrorEmail($order_id, 'Accfarm authentication failed!');

            $order->update_meta_data('_accfarm_order_error', 'Accfarm authentication failed!');

            $order->set_status('on-hold');
            $order->save();

            return;
        }

        $orderItems = $order->get_items();
        $item = array_pop($orderItems);

        if (empty($item)) {
            return;
        }

        $accfarmOrderNumber = $order->get_meta('_accfarm_order_number');

        if (!empty($accfarmOrderNumber)) {
            $this->updateOrder($order, $accfarmOrderNumber);
            return;
        }

        $product_id = $item->get_product_id();
        $accfarmProductType = get_post_meta($product_id, '_accfarm_product_type', true);

        $requestData = [
            'offer_id' => (int) get_post_meta($product_id, '_accfarm_offer_id', true),
            'quantity' => $item['quantity'],
            'callback_url' => BasicRoutes::instance()->getCallbackLink(),
        ];

        if (get_option('accfarm_reseller_testing_use_sandbox', false)) {
            $requestData['sandbox'] = true;
        }

        switch ($accfarmProductType) {
            case 'review':
                $requestData += $this->getReviewData($order);
                break;
            case 'install':
                $requestData += $this->getInstallData($order);
                break;
        }

        $response = $this->accfarmApi->buy($accfarmProductType, $requestData);

        $buyLog = Log::instance()->log($response, 'buy');

        if (empty($response) || empty($response['status'])) {
            if (!empty($response['errors'])) {
                $order->update_meta_data('_accfarm_order_error', $response['errors']);
                $mailMessage = $response['errors'];
            } else {
                $mailMessage = "Buy request error occurred in order #$order_id! Needs checking!";
            }

            if (!empty($mailMessage)) {
                if (!empty($buyLog)) {
                    $mailMessage .= "<br> Log file name: $buyLog";
                }

                $this->sendErrorEmail($order_id, $mailMessage);
            }

            $order->set_status('on-hold');
            $order->save();

            return;
        }

        if ($response['status'] == 1 && !empty($response['order'])) {
            $order->update_meta_data('_accfarm_order', json_encode($response['order']));

            if (!empty($response['order']['number'])) {
                $order->update_meta_data('_accfarm_order_number', $response['order']['number']);
            }

            if (!is_null($response['order']['delivery_time'])) {
                $order->update_meta_data('_accfarm_order_delivery_time', $response['order']['delivery_time']);
            }

            if (!empty($response['order']['download_link'])) {
                $order->update_meta_data('_accfarm_order_download_link', $response['order']['download_link']);
                $order->update_meta_data('_accfarm_order_download_string', $this->getDownloadString($response['order']['download_link']));
            }

            if (empty($response['order']['delivery_time'])
                && !empty($response['order']['download_link'])
                && $accfarmProductType == 'offer') {
                $orderStatusToSet = get_option('accfarm_reseller_hooks_order_status_on_af_callback', 'completed');

                $order->set_status($orderStatusToSet);
            }
        }

        $order->save();

        do_action('accfarm_reseller_after_buy', $order);
    }

    public function getDownloadString($downloadLink): string
    {
        $downloadLinkArray = explode('/', $downloadLink);

        return $downloadLinkArray[count($downloadLinkArray) - 2] . $downloadLinkArray[count($downloadLinkArray) - 1];
    }

    public function singCallbackData($data)
    {
        ksort($data);

        $string = '';

        foreach($data as $value) {
            if (in_array(gettype($value), ['array', 'object', 'NULL']) ){
                continue;
            }
            if(is_bool($value) && $value){
                $string .= 1;
            } else {
                $string .= $value;
            }
        }

        return hash_hmac('sha512', strtolower($string), get_option('accfarm_reseller_reseller_secret'));
    }

    private function updateOrder($order, $orderNumber)
    {
        $request = $this->accfarmApi->order($orderNumber);

        Log::instance()->log($request, 'orderUpdate');

        if (empty($request['status'])) {
            return;
        }

        $accfarmOrder = $order->get_meta('_accfarm_order');

        if (!empty($accfarmOrder)) {
            $accfarmOrder = json_decode($accfarmOrder, true);
        }

        $accfarmOrder['status'] = $request['status'];
        $order->update_meta_data('_accfarm_order', json_encode($accfarmOrder));
        $order->save();
    }

    private function getReviewData($order): array
    {
        $data = [];

        $data['url'] = $order->get_meta('_review_url');

        if (!empty($reviews = $order->get_meta('_review_reviews'))) {
            $data['reviews'] = $reviews;
        }

        return $data;
    }

    private function getInstallData($order): array
    {
        $data = [];

        $data['app_link'] = $order->get_meta('_install_app_link');
        $data['app_id'] = $order->get_meta('_install_app_id');
        $data['days'] = $order->get_meta('_install_days');
        $data['country'] = $order->get_meta('_install_country');

        if (!empty($reviews = $order->get_meta('_install_reviews'))) {
            $data['reviews'] = $reviews;
        }

        return $data;
    }

    private function sendErrorEmail(int $orderNumber, string $error)
    {
        $subject = "Paid Order #$orderNumber has an error!";
        $mailer = WC()->mailer();

        $mailer->send(
            get_site_option('admin_email'),
            $subject,
            $mailer->wrap_message($subject, $error),
            '',
            ''
        );
    }
}