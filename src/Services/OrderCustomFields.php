<?php

namespace Src\Services;

use Src\Traits\Singleton;

class OrderCustomFields
{
    use Singleton;

    public function register()
    {
        add_filter('woocommerce_account_orders_columns', [$this, 'add_account_orders_column'], 10, 1);
        add_action('woocommerce_my_account_my_orders_column_accounts-link', [$this, 'add_account_orders_column_rows']);

        add_action('woocommerce_order_details_after_order_table', [$this, 'order_details'], 10, 1);
    }

    public function add_account_orders_column($columns): array
    {
        $key = 'order-actions';
        $offset = array_search($key, array_keys($columns));

        return array_merge
        (
            array_slice($columns, 0, $offset),
            ['accounts-link' => __('Accounts Link', 'accfarm-reseller')],
            array_slice($columns, $offset, null)
        );
    }

    public function add_account_orders_column_rows($order)
    {
        $downloadLink = BasicRoutes::instance()->getDownloadAccountsLink($order);

        if (!empty($downloadLink)) {
            echo '<a href="' . $downloadLink . '">' . __('Download', 'accfarm-reseller') . '</a>';
        }
    }

    public function order_details($order)
    {
        $downloadLink = BasicRoutes::instance()->getDownloadAccountsLink($order);

        if (empty($downloadLink)) {
            return;
        }

        ?>
        <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
            <li class="woocommerce-order-overview__order order">
                <strong>
                    <?php _e('Accounts Download Link', 'accfarm-reseller'); ?>:
                    <a href="<?php echo $downloadLink;?>">
                        <?php _e('download', 'accfarm-reseller'); ?>
                    </a>
                </strong>
            </li>
        </ul>
        <?php
    }
}