<?php

namespace Src\Pages;

class Admin
{
    /**
     * @var string
     */
    public static $page = 'accfarm_reseller';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_init', [$this, 'register_plugin_settings']);
    }

    public function add_admin_pages()
    {
        add_menu_page(
            __('Accfarm Reseller', 'accfarm-reseller'),
            __('Accfarm Reseller', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            function () {},
            'dashicons-store',
            58
        );

        add_submenu_page(
            'accfarm_reseller',
            __('General', 'accfarm-reseller'),
            __('General', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            [$this, 'template'],
            58
        );
    }

    public function template()
    {
        require_once ACCFARM_RESELLER_PATH . 'templates/admin.php';
    }

    public function register_plugin_settings()
    {
        add_settings_section(
            self::$page . '_general',
            __('Authentication', 'accfarm-reseller'),
            function () {},
            self::$page
        );

        add_settings_field(
            self::$page . '_reseller_email',
            __('Api Email', 'accfarm-reseller'),
            [$this, 'reseller_email_callback'],
            self::$page,
            self::$page . '_general',
            ['label_for' => self::$page . '_reseller_email']
        );

        add_settings_field(
            self::$page . '_reseller_password',
            __('Api Password', 'accfarm-reseller'),
            [$this, 'reseller_password_callback'],
            self::$page,
            self::$page . '_general',
            ['label_for' => self::$page . '_reseller_password']
        );

        add_settings_field(
            self::$page . '_reseller_secret',
            __('Api Secret', 'accfarm-reseller'),
            [$this, 'reseller_secret_callback'],
            self::$page,
            self::$page . '_general',
            ['label_for' => self::$page . '_reseller_secret']
        );

        register_setting(self::$page . '_general', self::$page . '_reseller_email');
        register_setting(self::$page . '_general', self::$page . '_reseller_password');
        register_setting(self::$page . '_general', self::$page . '_reseller_secret');
    }

    public function reseller_email_callback()
    {
        $val = get_option('accfarm_reseller_reseller_email');
        echo '<input type="text" name="' . 'accfarm_reseller_reseller_email' . '" id="' . 'accfarm_reseller_reseller_email' . '" value="' . $val . '"> ';
    }

    public function reseller_password_callback()
    {
        $val = get_option('accfarm_reseller_reseller_email');
        echo '<input type="password" name="' . 'accfarm_reseller_reseller_password' . '" id="' . 'accfarm_reseller_reseller_password' . '" value="' . $val . '"> ';
    }

    public function reseller_secret_callback()
    {
        $val = get_option('accfarm_reseller_reseller_secret');
        echo '<input type="text" name="' . 'accfarm_reseller_reseller_secret' . '" id="' . 'accfarm_reseller_reseller_secret' . '" value="' . $val . '"> ';
    }
}