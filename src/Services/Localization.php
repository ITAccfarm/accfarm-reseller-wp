<?php

namespace Src\Services;

use Src\Traits\Singleton;

class Localization
{
    use Singleton;

    public function register()
    {
        add_action('init', [$this, 'accfarm_reseller_load_textdomain']);
    }

    public function accfarm_reseller_load_textdomain() {
        load_plugin_textdomain('accfarm-reseller',
            false,
            dirname(plugin_basename(ACCFARM_RESELLER_FILE)) . '/i18n/languages'
        );
    }
}