<?php

namespace Src\Pages;

class Hooks
{
    /**
     * @var string
     */
    public static $page = 'accfarm_reseller_hooks';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_init', [$this, 'register_plugin_settings']);
    }

    public function add_admin_pages()
    {
        add_submenu_page(
            'accfarm_reseller',
            __('Hooks', 'accfarm-reseller'),
            __('Hooks', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            [$this, 'template'],
            58
        );
    }

    public function template()
    {
        require_once ACCFARM_RESELLER_PATH . 'templates/hooks.php';
    }

    public function register_plugin_settings()
    {
        add_settings_section(
            self::$page . '_order_statuses',
            __('Order Statuses', 'accfarm-reseller'),
            function () {},
            self::$page
        );

        add_settings_field(
            self::$page . '_order_status_to_buy_af',
            __('Order status to Accfarm buy', 'accfarm-reseller'),
            [$this, 'order_status_to_buy_af_callback'],
            self::$page,
            self::$page . '_order_statuses',
            ['label_for' => self::$page . '_order_status_to_buy_af']
        );

        add_settings_field(
            self::$page . '_order_status_on_af_callback',
            __('Order status on Accfarm callback received', 'accfarm-reseller'),
            [$this, 'order_status_on_af_callback_callback'],
            self::$page,
            self::$page . '_order_statuses',
            ['label_for' => self::$page . '_order_status_on_af_callback']
        );

        register_setting(self::$page . '_order_statuses', self::$page . '_order_status_to_buy_af');
        register_setting(self::$page . '_order_statuses', self::$page . '_order_status_on_af_callback');
    }

    public function order_status_to_buy_af_callback()
    {
        $val = get_option('accfarm_reseller_hooks_order_status_to_buy_af', 'processing');

        $statuses = [
            'pending',
            'processing',
            'on-hold',
            'completed',
            'cancelled',
            'refunded',
            'failed',
        ];

        echo '<select name="accfarm_reseller_hooks_order_status_to_buy_af">';

        foreach ($statuses as $status) {
            echo '<option ';

            if ($val === $status) {
                echo 'selected ';
            }

            echo 'value="' . $status . '">' . ucfirst($status) . '</option>';
        }

        echo '</select>';
    }

    public function order_status_on_af_callback_callback()
    {
        $val = get_option('accfarm_reseller_hooks_order_status_on_af_callback', 'completed');

        $statuses = [
            'pending',
            'processing',
            'on-hold',
            'completed',
            'cancelled',
            'refunded',
            'failed',
        ];

        echo '<select name="accfarm_reseller_hooks_order_status_on_af_callback">';

        foreach ($statuses as $status) {
            echo '<option ';

            if ($val === $status) {
                echo 'selected ';
            }

            echo 'value="' . $status . '">' . ucfirst($status) . '</option>';
        }

        echo '</select>';
    }
}