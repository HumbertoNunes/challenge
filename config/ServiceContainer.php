<?php

namespace Config;

use DI\Container;

class ServiceContainer
{
    private static $container;

    /**
     * Setup the service container
     *
     * @param Container $container
     *
     * @return void
     */
    public static function set(Container $container): void
    {
        self::$container = $container;
    }

    /**
     * Retrieves a item from the container
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get(string $key)
    {
        return self::$container->get($key);
    }
}
