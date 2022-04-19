<?php

namespace Src\Services;

use Src\Traits\Singleton;

class OrderAdminFields
{
    use Singleton;

    public function register()
    {
        add_action('add_meta_boxes', [$this, 'order_metabox']);
    }

    public function order_metabox()
    {
        add_meta_box(
                'accfarm_order_fields',
                __('Accfarm Order', 'accfarm-reseller'),
                [$this, 'order_admin_custom_fields'],
            'shop_order',
                'side',
                'core'
        );
    }

    public function order_admin_custom_fields($order)
    {
        global $post;

        $order = wc_get_order($post->ID);

        $accfarmOrder = $order->get_meta('_accfarm_order');
        $orderNumber = $order->get_meta('_accfarm_order_number');
        $downloadLink = BasicRoutes::instance()->getDownloadAccountsLink($order);
        $deliveryTime = $order->get_meta('_accfarm_order_delivery_time');
        $orderError = $order->get_meta('_accfarm_order_error');

        if (!empty($accfarmOrder)) {
            $accfarmOrder = json_decode($accfarmOrder, true);
        }

        ?>

        <?php if (!empty($orderNumber)): ?>
            <p>
                <strong><?php _e('Order Number', 'accfarm-reseller');?>: </strong> <?php echo $orderNumber; ?>

                <?php if (!empty($accfarmOrder)): ?>
                    <br> <strong><?php _e('Order Status', 'accfarm-reseller');?>: </strong> <?php echo $accfarmOrder['status']; ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($accfarmOrder)): ?>
            <p>
                <strong>Offer: </strong>
                <br> <strong><?php _e('ID', 'accfarm-reseller'); ?>: </strong> <?php echo $accfarmOrder['offer']['id']; ?>
                <br> <strong><?php _e('Name', 'accfarm-reseller'); ?>: </strong> <br> <?php echo $accfarmOrder['offer']['name']; ?>
                <br> <strong><?php _e('Product Name', 'accfarm-reseller'); ?>: </strong> <br> <?php echo $accfarmOrder['offer']['product_name']; ?>
                <br> <strong><?php _e('Category Name', 'accfarm-reseller'); ?>: </strong> <br> <?php echo $accfarmOrder['offer']['category_name']; ?>
                <br> <strong><?php _e('Url', 'accfarm-reseller'); ?>: </strong> <a href="<?php echo $accfarmOrder['offer']['url']; ?>"><?php _e('Link', 'accfarm-reseller'); ?></a>
                <br> <strong><?php _e('Quantity', 'accfarm-reseller'); ?>: </strong> <?php echo $accfarmOrder['offer']['quantity']; ?>
                <br> <strong><?php _e('Price', 'accfarm-reseller'); ?>: </strong> <?php echo $accfarmOrder['offer']['price']; ?>
            </p>
        <?php endif; ?>

        <p>
            <?php if (!empty($downloadLink)): ?>
                <strong>
                    <?php _e('Accounts Download Link', 'accfarm-reseller'); ?>:
                    <a href="<?php echo $downloadLink;?>">
                        <?php _e('download', 'accfarm-reseller'); ?>
                    </a>
                </strong>
            <?php endif; ?>

            <?php if (!empty($deliveryTime)): ?>
                <strong>
                    <?php _e('Delivery Time', 'accfarm-reseller'); ?>:
                </strong>

                <?php echo $deliveryTime;?>
            <?php endif; ?>
        </p>

        <p>
            <?php if (!empty($orderError) && empty($accfarmOrder)): ?>
                <strong>
                    <?php echo __('Error!', 'accfarm-reseller') . ' ' . $orderError?>:
                </strong>
            <?php endif; ?>
        </p>

        <?php
    }
}