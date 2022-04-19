<?php

namespace Src\Services;

use Src\Traits\Singleton;

class Log
{
    use Singleton;

    /**
     * @var bool
     */
    private $doLog;

    /**
     * @var string
     */
    private $logFolder;

    public function register() {}

    public function __construct()
    {
        $this->doLog = get_option('accfarm_reseller_testing_store_logs', false);
        $this->logFolder = ACCFARM_RESELLER_PATH . 'logs/';
    }

    public function log(array $data, string $type = 'default'): string
    {
        $name = time() . "_$type.log";

        if ($this->doLog) {
            file_put_contents(
                $this->folder($name),
                json_encode($data, JSON_PRETTY_PRINT)
            );
        }

        return $name;
    }

    public function test(array $data)
    {
        $log = '';

        if (file_exists($this->folder('default.log'))) {
            $log = file_get_contents($this->folder('default.log'));
            $log .=  date('Y-m-d H:i:s') . PHP_EOL;
        };

        $log .= json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents(
            $this->folder('default.log'),
            $log . (PHP_EOL . PHP_EOL)
        );
    }

    public function folder(string $path = ''): string
    {
        return $this->logFolder . $path;
    }
}