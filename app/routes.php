<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\HelloController;
use Slim\App;

return function (App $app) {
	// unprotected routes
	$app->post('/register', AuthController::class . ':register');
	$app->post('/login', AuthController::class . ':login');

    $app->get('/users', UserController::class . ':index');
    $app->get('/users/{user}', UserController::class . ':show');
    $app->post('/users', UserController::class . ':store');

    // protected routes
    $app->get('/bye/{name}', HelloController::class . ':bye');

};
