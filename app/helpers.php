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

function dd($arguments)
{
	die(var_dump($arguments));
}