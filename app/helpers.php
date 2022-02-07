<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;

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

function dd($arguments)
{
	die(var_dump($arguments));
}