<?php

declare(strict_types=1);

namespace Tests\Feature;

use Slim\Exception\HttpUnauthorizedException;
use Tests\BaseTestCase;

class AuthTest extends BaseTestCase
{
    /**
     * @var \Slim\App
     */
    protected $app;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app = $this->getAppInstance();
    }

    /** @test */
    public function it_should_authenticate_the_user()
    {
        // Given
        $user = ['email' => 'humberto@mail', 'password' => '210992'];

        // When
        $request = $this->visit('POST', '/login')->withParsedBody($user);

        // Then
        $response = (string) $this->app->handle($request)->getBody();
        $this->parse($response);

        $this->assertEquals(
            "{$user['email']}:{$user['password']}",
            base64_decode(str_replace('Bearer ', '', $response))
        );
    }
}
