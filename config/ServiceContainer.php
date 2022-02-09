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
        return self::$container->has($key) ? self::$container->get($key) : null;
    }

    /**
     * Add a new item to the container
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public static function add($key, $value): void
    {
        self::$container->set($key, $value);
    }
}
