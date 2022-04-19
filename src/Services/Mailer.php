<?php

namespace Src\Services;

use Src\Traits\Singleton;

class Mailer
{
    use Singleton;

    public function register()
    {
        add_filter('woocommerce_email_format_string' , [$this, 'custom_formatting'], 10, 2);
    }

    function custom_formatting($string, $email)
    {
        $placeholder = '{accounts_link_or_delivery_time}';

        $order = $email->object;
        $deliveryTime = $order->get_meta('_accfarm_order_delivery_time');
        $downloadLink = BasicRoutes::instance()->getDownloadAccountsLink($order);
        $orderFinishedStatus = get_option('accfarm_reseller_hooks_order_status_on_af_callback', 'completed');

        $productType = $this->productType($order);

        if (empty($productType) || $productType != 'offer') {
            return str_replace($placeholder, '', $string);
        }

        if (!empty($downloadLink)) {
            return $this->processLink($string, $downloadLink, $placeholder);
        }

        if (!empty($deliveryTime) && $order->get_status() != $orderFinishedStatus) {
            return $this->processDelivery($string, $deliveryTime, $placeholder);
        }

        return str_replace($placeholder, '', $string);
    }

    private function processLink($string, $downloadLink, $placeholder)
    {
        $downloadLinkString = get_option('accfarm_reseller_mail_accounts_link', '');

        if (empty($downloadLinkString)) {
            return str_replace($placeholder, '', $string);
        }

        $downloadLinkString = str_replace('{link}', $downloadLink, $downloadLinkString);

        return str_replace($placeholder, $downloadLinkString, $string);
    }

    private function processDelivery($string, $deliveryTime, $placeholder)
    {
        $deliveryTimeString = get_option('accfarm_reseller_mail_accounts_delivery_time', '');

        if (empty($deliveryTimeString)) {
            return str_replace($placeholder, '', $string);
        }

        $deliveryTimeString = str_replace('{hours_number}', $deliveryTime, $deliveryTimeString);

        return str_replace($placeholder, $deliveryTimeString, $string);
    }

    private function productType($order)
    {
        $orderItems = $order->get_items();
        $item = array_pop($orderItems);

        if (empty($item)) {
            return '';
        }

        $product_id = $item->get_product_id();
        return get_post_meta($product_id, '_accfarm_product_type', true);
    }
}