<?php

namespace Src\Services;

use Src\Traits\Singleton;

class LogExtractor
{
    use Singleton;

    public function register() {}

    public function all(): array
    {
        $dir = scandir(Log::instance()->folder(), 1);
        $dir = $this->filter($dir);

        return $this->processAll($dir);
    }

    private function processAll(array $dir): array
    {
        $data = [];

        foreach ($dir as $file) {
            $data[] = $this->processLog($file);
        }

        return $data;
    }

    private function processLog(string $logFile): array
    {
        $fileArray = explode('_', $logFile);

        return [
            'data' => file_get_contents(Log::instance()->folder($logFile)),
            'type' => ucfirst(explode('.', $fileArray[1])[0]),
            'date' => date('Y-m-d H:i:s', $fileArray[0]),
        ];
    }

    private function filter(array $dir): array
    {
        return array_filter($dir, function ($value) {
             return $value[0] != '.' && $value != 'default.log';
        });
    }
}