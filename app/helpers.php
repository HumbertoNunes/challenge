<?php

use App\Models\User;
use Config\ServiceContainer;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;

/**
 * Returns the current user signed in
 *
 * @return mixed
 */
function auth()
{
    if ($auth = ServiceContainer::get('auth')) {
        return User::query()->whereEmail($auth)->first();
    }
}

/**
 * Returns a Connection instance
 *
 * @return Manager
 */
function connection(): Manager
{
    return \Config\ServiceContainer::get('db');
}

/**
 * Returns a builder instance
 *
 * @return Builder
 */
function query(): Builder
{
    return \Config\ServiceContainer::get('db')->table(null);
}

/**
 * Returns a factory class
 *
 * @return mixed 
 */
function factory(string $factoryClass, array $attributes = [])
{
    $faker = \Faker\Factory::create();

    return new $factoryClass($faker, $attributes);
}

function dd()
{
    foreach (func_get_args() as $argument) {
        var_dump($argument);
    }
    die();
}