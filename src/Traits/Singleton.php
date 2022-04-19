<?php

namespace Src\Traits;

trait Singleton
{
    /**
     * @var self
     */
    protected static $instance = null;

    /**
     * @return self
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}