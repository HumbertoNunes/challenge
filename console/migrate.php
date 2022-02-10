<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Database\Migrations\Migration;
use Illuminate\Database\Schema\Builder;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../app/helpers.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$ENV = $_ENV['ENV'] ?? 'dev';

$containerBuilder = new ContainerBuilder();

// Import services
$dependencies = require __DIR__ . '/../app/services.php';
$dependencies($containerBuilder);

// Initialize app with PHP-DI
$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();

// Migration Command Line Service
$method = $argv[1] ?? 'up';
$driver = str_replace('driver:', '', $argv[2] ?? '');

if (!in_array($method, ['up', 'down'])) {
    die('Command not found! Migrate accepts only up/down options' . PHP_EOL);
}

if (!empty($driver)) {
    $_ENV['DATABASE_DRIVER'] = $driver;
}

$builder = $app->getContainer()->get(Builder::class);

Migration::$method($builder);
