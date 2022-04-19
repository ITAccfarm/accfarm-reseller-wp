<?php

namespace Src\Pages;

class Import
{
    /**
     * @var string
     */
    public static $page = 'accfarm_reseller_import';

    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_pages']);
    }

    public function add_admin_pages()
    {
        add_submenu_page(
            'accfarm_reseller',
            __('Import', 'accfarm-reseller'),
            __('Import', 'accfarm-reseller'),
            'manage_options',
            self::$page,
            [$this, 'template'],
            58
        );
    }

    public function template()
    {
        require_once ACCFARM_RESELLER_PATH . 'templates/import.php';
    }
}