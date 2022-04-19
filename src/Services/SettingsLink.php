<?php

namespace Src\Services;

use Src\Traits\Singleton;

class SettingsLink
{
    use Singleton;

    public function register()
    {
        add_filter('plugin_action_links_' . ACCFARM_RESELLER_NAME, [$this, 'settings_link']);
    }

    public function settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=accfarm_reseller">' . __('Settings', 'accfarm-reseller') . '</a>';
        array_push( $links, $settings_link );

        return $links;
    }
}