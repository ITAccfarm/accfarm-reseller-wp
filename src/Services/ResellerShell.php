<?php

namespace Src\Services;

use ITAccfarm\ResellerSDK\ResellerSDK;

class ResellerShell
{
    /**
     * @var ResellerSDK
     */
    private $accfarmApi;

    public function __construct()
    {
        $this->accfarmApi = new ResellerSDK();
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->accfarmApi, $name)) {
            return [];
        }

        $response = call_user_func_array([$this->accfarmApi, $name], $arguments);

        if (is_array($response) && !empty($response['error'])
            && ($response['error'] == 'token_invalid' || $response['error'] == 'token_not_provided')) {
            Accfarm::instance()->invalidateToken();
            Accfarm::instance()->authenticate();
        }

        return call_user_func_array([$this->accfarmApi, $name], $arguments);
    }
}