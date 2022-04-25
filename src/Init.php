<?php

namespace Src;

use Src\Pages\Admin;
use Src\Pages\Hooks;
use Src\Pages\Import;
use Src\Pages\Logs;
use Src\Pages\Mail;
use Src\Pages\Settings;
use Src\Pages\Testing;
use Src\Services\Accfarm;
use Src\Services\Buy;
use Src\Services\CartLimit;
use Src\Services\CheckoutCustomFields;
use Src\Services\Enqueue;
use Src\Services\ImportOffers;
use Src\Services\Log;
use Src\Services\LogExtractor;
use Src\Services\Mailer;
use Src\Services\ThankYouCustomFields;
use Src\Services\BasicRoutes;
use Src\Services\Localization;
use Src\Services\OrderAdminFields;
use Src\Services\OrderCustomFields;
use Src\Services\OrderCustomMetaQuery;
use Src\Services\ProductCustomFields;
use Src\Services\SettingsLink;

class Init
{
    /**
     * @var array
     */
    private $services = [
        Localization::class,
        Accfarm::class,
        Log::class,
        LogExtractor::class,
        Enqueue::class,

        Buy::class,
        Mailer::class,
        CartLimit::class,
        BasicRoutes::class,
        ImportOffers::class,
        SettingsLink::class,
        OrderAdminFields::class,
        OrderCustomFields::class,
        ProductCustomFields::class,
        ThankYouCustomFields::class,
        OrderCustomMetaQuery::class,
        CheckoutCustomFields::class,
    ];

    /**
     * @var array
     */
    private $pages = [
        Admin::class,
        Import::class,
        Hooks::class,
        Mail::class,
        Logs::class,
        Testing::class,
        Settings::class,
    ];

    public function init()
    {
        $this->initServices();
        $this->initPages();
    }

    private function initServices()
    {
        foreach ($this->services as $serviceClass) {
            if (!method_exists($serviceClass, 'instance') || !method_exists($serviceClass, 'register')) {
                continue;
            }

            $serviceClass::instance()->register();
        }
    }

    private function initPages()
    {
        foreach ($this->pages as $pageClass) {
            if (!method_exists($pageClass, 'register')) {
                continue;
            }

            (new $pageClass())->register();
        }
    }
}