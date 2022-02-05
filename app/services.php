<?php
declare(strict_types=1);

use App\HelloController;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        Swift_Mailer::class => function () {
            $host = $_ENV['MAILER_HOST'] ?? 'smtp.mailtrap.io';
            $port = intval($_ENV['MAILER_PORT']) ?? 465;
            $username = $_ENV['MAILER_USERNAME'] ?? 'test';
            $password = $_ENV['MAILER_PASSWORD'] ?? 'test';

            $transport = (new Swift_SmtpTransport($host, $port))
                ->setUsername($username)
                ->setPassword($password);

            return new Swift_Mailer($transport);
        },
        'db' => function () {
            $capsule = new \Illuminate\Database\Capsule\Manager;
            $capsule->addConnection([
                'driver' => $_ENV['DATABASE_DRIVER'],
                'host' => $_ENV['DATABASE_HOST'],
                'database' => $_ENV['DATABASE_DATABASE'],
                'username' => $_ENV['DATABASE_USERNAME'],
                'password' => $_ENV['DATABASE_PASSWORD'],
                'charset'   => $_ENV['DATABASE_CHARSET'],
                'collation' => $_ENV['DATABASE_COLLATION'],
                'prefix'    => $_ENV['DATABASE_PREFIX'],
            ]);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        },
        HelloController::class => function ($container) {
            $table = $container->get('db')->table('users');
            return new HelloController($table);
        }
    ]);
};
