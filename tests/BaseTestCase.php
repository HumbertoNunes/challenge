<?php

declare(strict_types=1);

namespace Tests;

use Config\ServiceContainer;
use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;
use Symfony\Component\Dotenv\Dotenv;

class BaseTestCase extends PHPUnit_TestCase
{
    /**
     * @return App
     * @throws Exception
     */
    protected function getAppInstance(): App
    {
        parent::setUp();

        $containerBuilder = new ContainerBuilder();

        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env');

        require_once __DIR__ . '/../app/helpers.php';

        $dependencies = require __DIR__ . '/../app/services.php';
        $dependencies($containerBuilder);

        $container = $containerBuilder->build();
        AppFactory::setContainer($container);
        ServiceContainer::set($container);

        $app = AppFactory::create();

        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);

        $auth = require __DIR__ . '/../app/auth.php';
        $auth($app);

        return $app;
    }

    /**
     * @return String
     */
    protected function getAuthorizationHeader(): String
    {
        $adminTestingUsername = $_ENV["ADMIN_USERNAME"];
        $adminTestingPassword = $_ENV["ADMIN_PASSWORD"];


        return 'Basic ' . base64_encode("$adminTestingUsername:$adminTestingPassword");
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $headers
     * @param array  $cookies
     * @param array  $serverParams
     * @return Request
     */
    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', 'localhost', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    /**
     * Syntax sugar for createRequest function
     *
     * @return self
     */
    public function visit(): self
    {
        $this->request = $this->createRequest(...func_get_args());

        return $this;
    }

    /**
     * Syntax sugar for withParsedBody function.
     *
     * @param array $formData
     *
     * @return self
     */
    public function with(array $formData): self
    {
        $this->request = $this->request->withParsedBody($formData);

        return $this;
    }

    /**
     * Syntax sugar for the App handle function
     *
     * @return Response 
     */
    public function handle(): Response
    {
        return $this->app->handle($this->request);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        if (in_array('Tests\Helpers\RefreshDatabase', class_uses($this))) {
            $this->refreshDatabase();
        }
    }
}
