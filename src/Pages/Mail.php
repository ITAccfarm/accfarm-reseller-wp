<?php

namespace Src\Pages;

class Mail
{
    /**
     * @var string
     */
    public static $page = 'accfarm_reseller_mail';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_init', [$this, 'register_plugin_settings']);
    }

    public function add_admin_pages()
    {
        add_submenu_page(
            'accfarm_reseller',
            __('Mail', 'accfarm-reseller'),
            __('Mail', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            [$this, 'template'],
            58
        );
    }

    public function template()
    {
        require_once ACCFARM_RESELLER_PATH . 'templates/mail.php';
    }

    public function register_plugin_settings()
    {
        add_settings_section(
            self::$page . '_data',
            __('Email Text', 'accfarm-reseller'),
            function () {},
            self::$page
        );

        add_settings_field(
            self::$page . '_info',
            __('How to', 'accfarm-reseller'),
            [$this, '_info'],
            self::$page,
            self::$page . '_data',
            ['label_for' => self::$page . '_info']
        );

        add_settings_field(
            self::$page . '_accounts_link',
            __('Link with accounts text', 'accfarm-reseller'),
            [$this, '_accounts_link'],
            self::$page,
            self::$page . '_data',
            ['label_for' => self::$page . '_accounts_link']
        );

        add_settings_field(
            self::$page . '_accounts_delivery_time',
            __('Delivery time for accounts text', 'accfarm-reseller'),
            [$this, '_accounts_delivery_time'],
            self::$page,
            self::$page . '_data',
            ['label_for' => self::$page . '_accounts_delivery_time']
        );

        register_setting(self::$page . '_data', self::$page . '_accounts_link');
        register_setting(self::$page . '_data', self::$page . '_accounts_delivery_time');
    }

    public function _info()
    {
        echo "
            <p>".__('You need to write texts that will replace {accounts_link_or_delivery_time} placeholder in WooCommerce emails.', 'accfarm-reseller')."</p>
            <p>".__('Depending on a situation Accfarm can provide accounts immediately or will provide delivery time.', 'accfarm-reseller')."</p>
            <p>".__('If accounts are ready this placeholder {accounts_link_or_delivery_time} will return your "Link with accounts text".', 'accfarm-reseller')."</p>
            <p>".__('And if they are not and Accfarm provided Delivery time it will show "Delivery time for accounts text".', 'accfarm-reseller')."</p>
            <p>".__('This works only for "offer" products and this placeholder will not be shown in other product types.', 'accfarm-reseller')."</p>
        ";
    }

    public function _accounts_link()
    {
        $name = self::$page . '_accounts_link';
        $val = get_option($name, '');

        echo "
            <p>".__('It need to contain {link} placeholder that will be replaced with download link.', 'accfarm-reseller')."</p>
            <p> ".__('For example you can write: <i>Accounts download link: {link}</i>', 'accfarm-reseller')."</p> <br>
            <input style=\"width: 50%;\" type=\"text\" value=\"$val\" name=\"$name\" id=\"$name\">
        ";
    }

    public function _accounts_delivery_time()
    {
        $name = self::$page . '_accounts_delivery_time';
        $val = get_option($name, '');

        echo "
            <p>".__('It need to contain {hours_number} placeholder that will be replaced with number of hours to deliver this order.', 'accfarm-reseller')."</p>
            <p> ".__('For example you can write: <i>Delivery time for this order is {hours_number} hours.</i>', 'accfarm-reseller')."</p> <br>
            <input style=\"width: 50%;\" type=\"text\" value=\"$val\" name=\"$name\" id=\"$name\">
        ";
    }
}