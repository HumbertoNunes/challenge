<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\StockController;
use App\Controllers\UserController;
use Slim\App;

return function (App $app) {
    // unprotected routes
    $app->post('/register', AuthController::class . ':register');
    $app->post('/login', AuthController::class . ':login');

    // protected routes
    $app->get('/stock', StockController::class . ':stock');
    $app->get('/history', StockController::class . ':history');

    $app->get('/users', UserController::class . ':index');
    $app->get('/users/{user}', UserController::class . ':show');
    $app->post('/users', UserController::class . ':store');
};
