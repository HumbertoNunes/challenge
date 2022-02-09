<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Config\ServiceContainer;
use DI\ContainerBuilder;
use Database\Factories\UserFactory;
use Exception;
use Firebase\JWT\JWT;
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
    protected function getAuthorizationHeader(): string
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
     * Authenticate the user with a JWT
     *
     * @return self
     */
    public function login(array $credentials = []): self
    {
        $user = !empty($credentials) ? new User($credentials) : factory(UserFactory::class, $credentials)->create();

        $jwt = 'Bearer ' . JWT::encode($user->email, $_ENV['APP_KEY'], 'HS256');

        $this->headers = [['Authorization' => $jwt]];

        return $this;
    }

    /**
     * Syntax sugar for createRequest function
     *
     * @return self
     */
    public function visit(): self
    {
        $arguments = array_merge(func_get_args(), $this->headers ?? []);

        $this->request = $this->createRequest(...$arguments);

        return $this;
    }

    /**
     * Syntax sugar for passing data through the QueryString or the Request Body.
     *
     * @param array $data
     *
     * @return self
     */
    public function with(array $data): self
    {
        $with = [
            'GET' => 'withQueryParams',
            'POST' => 'withParsedBody'
        ][$this->request->getMethod()];

        $this->request = $this->request->$with($data);

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
