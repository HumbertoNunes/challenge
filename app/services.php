<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Database\Schema\Grammars\SQLiteGrammar;

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
        'db' => function ($container) {
            $capsule = new \Illuminate\Database\Capsule\Manager();
            $connections = $container->get('connections');
            $capsule->addConnection($connections[$_ENV['DATABASE_DRIVER']]);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        },
        'connections' => function () {
            return [
                'mysql' => [
                    'driver' => 'mysql',
                    'host' => $_ENV['DATABASE_HOST'],
                    'database' => $_ENV['DATABASE_DATABASE'],
                    'username' => $_ENV['DATABASE_USERNAME'],
                    'password' => $_ENV['DATABASE_PASSWORD'],
                    'charset'   => $_ENV['DATABASE_CHARSET'],
                    'collation' => $_ENV['DATABASE_COLLATION'],
                    'prefix'    => $_ENV['DATABASE_PREFIX'],
                ],
                'sqlite' => [
                    'driver' => 'sqlite',
                    'database' => __DIR__ . '/../database/database.sqlite',
                    'prefix'    => $_ENV['DATABASE_PREFIX'],
                ]
            ];
        },
        Builder::class => function ($container) {
            $connection = $container->get('db')->getConnection();
            $grammar = $container->get($_ENV['DATABASE_DRIVER']);

            $connection->setSchemaGrammar($grammar);

            return new Builder($container->get('db')->getConnection());
        },
        'mysql' => fn() => new MySqlGrammar(),
        'sqlite' => fn() => new SQLiteGrammar(),
        'postgres' => fn() => new PostgresGrammar(),
    ]);
};
