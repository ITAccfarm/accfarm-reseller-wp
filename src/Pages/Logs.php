<?php

namespace Src\Pages;

class Logs
{
    /**
     * @var string
     */
    public static $page = 'accfarm_reseller_logs';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
    }

    public function add_admin_pages()
    {
        add_submenu_page(
            'accfarm_reseller',
            __('Logs', 'accfarm-reseller'),
            __('Logs', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            [$this, 'template'],
            58
        );
    }

    public function template()
    {
        require_once ACCFARM_RESELLER_PATH . 'templates/logs.php';
    }
}