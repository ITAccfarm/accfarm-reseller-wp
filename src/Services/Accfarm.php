<?php

namespace Src\Services;

use ITAccfarm\ResellerSDK\ResellerSDK;
use Src\Traits\Singleton;

class Accfarm
{
    use Singleton;

    public function register() {}
    
    /**
     * @var ResellerSDK
     */
    private $accfarmApi;

    /**
     * @var bool
     */
    private $authed = false;

    public function __construct()
    {
        $this->accfarmApi = new ResellerShell();
    }

    public function api()
    {
        return $this->accfarmApi;
    }

    public function authenticate(): bool
    {
        if ($this->authed) {
            return true;
        }

        $token = $this->retrieveToken();

        if (!empty($token)) {
            $this->setToken($token['bearerToken'], $token['userSecret']);
            $this->authed = true;

            return true;
        }

        $token = $this->getToken();

        if (empty($token)) {
            return false;
        }

        $this->storeToken($token['bearerToken'], $token['userSecret']);
        $this->authed = true;

        return true;
    }

    private function getAuthData(): array
    {
        return [
            'email'     => get_option('accfarm_reseller_reseller_email', ''),
            'password'  => get_option('accfarm_reseller_reseller_password', ''),
        ];
    }

    private function getToken(): array
    {
        $authData = $this->getAuthData();

        $response = $this->accfarmApi->auth(
            $authData['email'],
            $authData['password']
        );

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    public function invalidateToken()
    {
        $this->authed = false;
        $this->storeToken('', '');
    }

    private function setToken(string $token, string $secret)
    {
        $this->accfarmApi->setToken($token);
        $this->accfarmApi->setSecret($secret);
    }

    private function retrieveToken()
    {
        if (!file_exists(ACCFARM_RESELLER_PATH . 'token/token.json')) {
            return [];
        }

        $tokenJson = file_get_contents(ACCFARM_RESELLER_PATH . 'token/token.json');

        if (empty($tokenJson)) {
            return [];
        }

        $tokenData = json_decode($tokenJson, true);

        if (empty($tokenData['time']) || empty($tokenData['bearerToken']) || empty($tokenData['userSecret'])) {
            return [];
        }

        if ((time() - $tokenData['time']) > (60 * 60)) {
            return [];
        }

        return $tokenData;
    }

    private function storeToken(string $token, string $secret)
    {
        $tokenData = [
            'bearerToken' => $token,
            'userSecret' => $secret,
            'time' => time(),
        ];

        file_put_contents(
            ACCFARM_RESELLER_PATH . 'token/token.json',
            json_encode($tokenData, JSON_PRETTY_PRINT)
        );
    }
}