<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Exception\HttpException;
use Slim\Exception\HttpUnauthorizedException;
use Tests\BaseTestCase;
use Tests\Helpers\RefreshDatabase;

class AuthTest extends BaseTestCase
{
    use RefreshDatabase;

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

    /**
     * @test
     */
    public function it_should_register_a_user()
    {
        // Given
        $credentials = factory(UserFactory::class)->make()->only(['email', 'password']);
        $credentials->put('password_confirmation', $credentials->get('password'));

        // When
        $response = $this->visit('POST', '/register')->with($credentials->all())->handle();

        // Then
        $this->assertEquals('User created', (string) $response->getBody());
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_should_authenticate_a_user()
    {
        // Given
        $credentials = factory(UserFactory::class, ['password' => 'Jobsity@2022'])->create()->only(['email', 'password']);
        $credentials->put('password', 'Jobsity@2022'); // The password before be encrypted

        // When
        $response = $this->visit('POST', '/login')->with($credentials->all())->handle();

        // Then
        $this->assertToken($credentials->get('email'), (string) $response->getBody());
    }

    /**
     * @test
     *
     * @expectedException HttpException
     */
    public function it_should_require_the_password_confirmation_on_register_user()
    {
        $credentials = factory(UserFactory::class, ['password' => 'Jobsity@2022'])->make()->only(['email', 'password']);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('The password_confirmation field is required.');

        $response = $this->visit('POST', '/register')->with($credentials->all())->handle();
    }

    /**
     * @test
     *
     * @expectedException HttpException
     */
    public function it_should_check_the_password_with_the_password_confirmation_on_register_user()
    {
        $credentials = factory(UserFactory::class, ['password' => 'Jobsity@2022'])->make()->only(['email', 'password']);
        $credentials->put('password_confirmation', 'some other password');

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Password must match the confirmation.');

        $response = $this->visit('POST', '/register')->with($credentials->all())->handle();
    }

    /**
     * @test
     *
     * @expectedException HttpUnauthorizedException
     */
    public function it_should_not_allow_the_user_sign_in_with_fake_email()
    {
        $user = factory(UserFactory::class)->make();
        $fake_credentials = $user->put('email', 'jobsity@mail.com')->all();

        $this->expectException(HttpUnauthorizedException::class);
        $this->expectExceptionCode(401);
        $this->expectExceptionMessage('Incorrect username or password.');

        $this->visit('POST', '/login')->with($fake_credentials)->handle();
    }

    /**
     * @test
     *
     * @expectedException HttpUnauthorizedException
     */
    public function it_should_not_allow_the_user_sign_in_with_wrong_password()
    {
        $user = factory(UserFactory::class)->make();
        $fake_credentials = $user->put('password', 'Jobsity@2021')->all();

        $this->expectException(HttpUnauthorizedException::class);
        $this->expectExceptionCode(401);
        $this->expectExceptionMessage('Incorrect username or password.');

        $this->visit('POST', '/login')->with($fake_credentials)->handle();
    }

    /**
     * @param string $email
     * @param string $jwt
     *
     * @return void
     */
    private function assertToken(string $email, string $jwt)
    {
        $decoded = JWT::decode($jwt, new Key($_ENV['APP_KEY'], 'HS256'));

        $this->assertEquals($email, $decoded);
    }
}
