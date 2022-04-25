<?php

namespace Src\Pages;

class Settings
{
    /**
     * @var string
     */
    public static $page = 'accfarm_reseller_settings';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_init', [$this, 'register_plugin_settings']);
    }

    public function add_admin_pages()
    {
        add_submenu_page(
            'accfarm_reseller',
            __('Settings', 'accfarm-reseller'),
            __('Settings', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            [$this, 'template'],
            58
        );
    }

    public function template()
    {
        require_once ACCFARM_RESELLER_PATH . 'templates/settings.php';
    }

    public function register_plugin_settings()
    {
        add_settings_section(
            self::$page . '_settings',
            __('Settings', 'accfarm-reseller'),
            function () {},
            self::$page
        );

        add_settings_field(
            self::$page . '_billing_fields',
            __('Do not show billing fields', 'accfarm-reseller'),
            [$this, 'reseller_billing_fields'],
            self::$page,
            self::$page . '_settings',
            ['label_for' => self::$page . '_billing_fields']
        );

        register_setting(self::$page . '_settings', self::$page . '_billing_fields');
    }

    public function reseller_billing_fields()
    {
        $val = get_option('accfarm_reseller_settings_billing_fields');

        $checked = '';

        if ($val) {
            $checked = 'checked';
        }

        echo '<input type="checkbox" name="' . 'accfarm_reseller_settings_billing_fields' . '" id="' . 'accfarm_reseller_settings_billing_fields' . '" ' . $checked . '>';
        echo '<label for="accfarm_reseller_settings_billing_fields"><strong>'
            . __('Will disable billing fields from checkout', 'accfarm-reseller')
            . '</label>';
    }
}