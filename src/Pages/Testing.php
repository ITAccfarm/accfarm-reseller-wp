<?php

namespace Src\Pages;

class Testing
{
    /**
     * @var string
     */
    public static $page = 'accfarm_reseller_testing';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_init', [$this, 'register_plugin_settings']);
    }

    public function add_admin_pages()
    {
        add_submenu_page(
            'accfarm_reseller',
            __('Testing', 'accfarm-reseller'),
            __('Testing', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            [$this, 'template'],
            58
        );
    }

    public function template()
    {
        require_once ACCFARM_RESELLER_PATH . 'templates/testing.php';
    }

    public function register_plugin_settings()
    {
        add_settings_section(
            self::$page . '_testing',
            __('Testing', 'accfarm-reseller'),
            function () {},
            self::$page
        );

        add_settings_field(
            self::$page . '_use_sandbox',
            __('Use Sandbox (not real orders)', 'accfarm-reseller'),
            [$this, 'reseller_use_sandbox_callback'],
            self::$page,
            self::$page . '_testing',
            ['label_for' => self::$page . '_use_sandbox']
        );

        add_settings_field(
            self::$page . '_store_logs',
            __('Store logs', 'accfarm-reseller'),
            [$this, 'reseller_store_logs'],
            self::$page,
            self::$page . '_testing',
            ['label_for' => self::$page . '_store_logs']
        );

        register_setting(self::$page . '_testing', self::$page . '_use_sandbox');
        register_setting(self::$page . '_testing', self::$page . '_store_logs');
    }

    public function reseller_use_sandbox_callback()
    {
        $val = get_option('accfarm_reseller_testing_use_sandbox');

        $checked = '';

        if ($val) {
            $checked = 'checked';
        }

        echo '<input type="checkbox" name="' . 'accfarm_reseller_testing_use_sandbox' . '" id="' . 'accfarm_reseller_testing_use_sandbox' . '" ' . $checked . '>';
        echo '<label for="accfarm_reseller_testing_use_sandbox"><strong>'
            . __('WARNING!', 'accfarm-reseller') . '</strong> '
            . __('Will enable sandbox mode for <strong>ALL</strong> orders. Even users will receive a fake order, if this enabled. Use carefully.', 'accfarm-reseller')
            . '</label>';
    }

    public function reseller_store_logs()
    {
        $val = get_option('accfarm_reseller_testing_store_logs');

        $checked = '';

        if ($val) {
            $checked = 'checked';
        }

        echo '<input type="checkbox" name="' . 'accfarm_reseller_testing_store_logs' . '" id="' . 'accfarm_reseller_testing_store_logs' . '" ' . $checked . '>';
        echo '<label for="accfarm_reseller_testing_use_sandbox">'
            . __('Will store logs in wp-content/plugins/accfarm-reseller/logs/ folder. Use carefully.', 'accfarm-reseller')
            . '</label>';
    }
}