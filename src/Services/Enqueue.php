<?php

namespace Src\Services;

use Src\Traits\Singleton;

class Enqueue
{
    use Singleton;

    public function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts($hook)
    {
        if ($hook == 'accfarm-reseller_page_accfarm_reseller_import') {
            wp_enqueue_script(
                'app-accfarm-import',
                plugin_dir_url(ACCFARM_RESELLER_FILE) . 'assets/js/app-accfarm-import.js',
                ['jquery', 'wp-util']
            );
        }

        if ($hook == 'accfarm-reseller_page_accfarm_reseller_logs') {
            wp_enqueue_script(
                'app-accfarm-import',
                plugin_dir_url(ACCFARM_RESELLER_FILE) . 'assets/js/app-accfarm-logs.js',
                ['jquery', 'wp-util']
            );
        }
    }
}