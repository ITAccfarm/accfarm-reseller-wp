<?php

namespace Src\Services;

use Src\Traits\Singleton;

class BasicRoutes
{
    use Singleton;

    public function register()
    {
        add_action('init',  function() {
            add_rewrite_rule(
                'arppages\/([a-z0-9-]+)\/?([a-z0-9-]+)?[\/]?$',
                'index.php?arppages=$matches[1]&arppagedata=$matches[2]',
                'top'
            );
        });

        add_filter('query_vars', function( $query_vars ) {
            $query_vars[] = 'arppages';
            $query_vars[] = 'arppagedata';
            return $query_vars;
        });

        add_action( 'template_include', function($template) {
            if (get_query_var('arppages') == '' || get_query_var('arppages') == false) {
                return $template;
            }

            if (get_query_var('arppages') == 'downloadaccounts' && !empty(get_query_var('arppagedata'))) {
                return ACCFARM_RESELLER_PATH . 'scripts' . DIRECTORY_SEPARATOR . 'download-accounts.php';
            }

            if (get_query_var('arppages') == 'callback') {
                return ACCFARM_RESELLER_PATH . 'scripts' . DIRECTORY_SEPARATOR . 'callback.php';
            }

            wp_redirect(home_url());
            exit;
        });
    }

    public function getDownloadAccountsLink($order): string
    {
        $downloadString = $order->get_meta('_accfarm_order_download_string');

        if (empty($downloadString)) {
            return '';
        }

        $orderStatus = get_option('accfarm_reseller_hooks_order_status_on_af_callback', 'completed');
        $deliveryTime = $order->get_meta('_accfarm_order_delivery_time');

        if ($order->get_status() == $orderStatus || empty($deliveryTime)) {
            return get_site_url() . '/arppages/downloadaccounts/' . $order->get_meta('_accfarm_order_download_string');
        }

        return '';
    }

    public function getCallbackLink(): string
    {
        return get_site_url() . '/arppages/callback/';
    }
}