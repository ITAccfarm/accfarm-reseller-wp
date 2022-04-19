<?php

namespace Src;

use Src\Traits\Singleton;

class AccfarmReseller
{
    use Singleton;

    public function init()
    {
        (new Init())->init();
    }

    public function activate()
    {
        flush_rewrite_rules();

        update_option('accfarm_reseller_hooks_order_status_to_buy_af', 'processing');
        update_option('accfarm_reseller_hooks_order_status_on_af_callback', 'completed');

        update_option('woocommerce_enable_guest_checkout', false);
        update_option('woocommerce_enable_checkout_login_reminder', 'yes');
        update_option('woocommerce_enable_signup_and_login_from_checkout', 'yes');
        update_option('woocommerce_enable_myaccount_registration', 'yes');

        update_option('woocommerce_cart_redirect_after_add', 'yes');
    }

    public function deactivate()
    {
        flush_rewrite_rules();
    }

    public function __clone() {}
    public function __wakeup() {}
}