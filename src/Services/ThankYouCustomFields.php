<?php

namespace Src\Services;

use Src\Traits\Singleton;

class ThankYouCustomFields
{
    use Singleton;

    public function register()
    {
        add_action('woocommerce_before_thankyou', [$this, 'custom_content_thankyou']);
    }

    public function custom_content_thankyou($order_id)
    {
        $order = wc_get_order($order_id);
        $orderItems = $order->get_items();
        $item = array_pop($orderItems);

        if (empty($item)) {
            return;
        }

        $product_id = $item->get_product_id();
        $accfarmProductType = get_post_meta($product_id, '_accfarm_product_type', true);

        if ($accfarmProductType != 'offer') {
            return;
        }

        $downloadLink = BasicRoutes::instance()->getDownloadAccountsLink($order);
        $deliveryTime = $order->get_meta('_accfarm_order_delivery_time');

        if (!empty($downloadLink)) {
            $this->link_ready($downloadLink);
        } else {
            $this->link_is_not_ready($deliveryTime);
        }
    }

    private function link_ready($downloadLink)
    {
        ?>
        <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

            <li class="woocommerce-order-overview__order order">
                <strong>
                    <?php _e('Accounts download link', 'accfarm-reseller'); ?>:
                    <a href="<?php echo $downloadLink; ?>">
                        <?php _e('download', 'accfarm-reseller');?>
                    </a>
                </strong>
            </li>
        </ul>
        <?php
    }

    private function link_is_not_ready($deliveryTime)
    {
        ?>
        <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
            <li class="woocommerce-order-overview__order order">
                <strong>
                    <?php _e('Your accounts will be ready soon!', 'accfarm-reseller'); ?>

                    <?php if (!empty($deliveryTime)): ?>
                        <br> <?php printf(__('Estimated delivery time is %s hours', 'accfarm-reseller'), $deliveryTime); ?>
                    <?php endif; ?>
                </strong>
            </li>
        </ul>
        <?php
    }
}