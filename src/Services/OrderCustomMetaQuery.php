<?php

namespace Src\Services;

use Src\Traits\Singleton;

class OrderCustomMetaQuery
{
    use Singleton;

    public function register()
    {
        add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', [$this, 'handle_custom_query_var'], 10, 2 );
    }

    public function handle_custom_query_var( $query, $query_vars ): array
    {
        if (!empty($query_vars['_accfarm_order_download_string'])) {
            $query['meta_query'][] = [
                'key' => '_accfarm_order_download_string',
                'value' => esc_attr($query_vars['_accfarm_order_download_string']),
            ];
        }

        if (!empty($query_vars['_accfarm_order_number'])) {
            $query['meta_query'][] = [
                'key' => '_accfarm_order_number',
                'value' => esc_attr($query_vars['_accfarm_order_number']),
            ];
        }

        return $query;
    }
}