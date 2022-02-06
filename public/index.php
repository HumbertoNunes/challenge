<?php

declare(strict_types=1);

use Config\ServiceContainer;
use DI\ContainerBuilder;
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

// Initialize app with PHP-DI and the ServiceContainer
$container = $containerBuilder->build();
AppFactory::setContainer($container);
ServiceContainer::set($container);

$app = AppFactory::create();

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

// Setup Basic Auth
$auth = require __DIR__ . '/../app/auth.php';
$auth($app);

$displayErrorDetails = $ENV == 'dev';
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

// Error Handler
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

$app->run();
