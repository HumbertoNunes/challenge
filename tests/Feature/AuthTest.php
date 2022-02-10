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
    public function itShouldRegisterAUser()
    {
        // Given
        $credentials = factory(UserFactory::class)->make()->only(['email', 'password']);
        $credentials->put('password_confirmation', $credentials->get('password'));

        // When
        $response = $this->visit('POST', '/register')->with($credentials->all())->handle();

        // Then
        $this->assertEquals('User created', json_decode((string) $response->getBody())->message);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function itShouldAuthenticateAUser()
    {
        // Given
        $credentials = factory(UserFactory::class, [
            'password' => 'Jobsity@2022'
        ])->create()->only(['email', 'password']);
        $credentials->put('password', 'Jobsity@2022'); // The password before be encrypted

        // When
        $response = $this->visit('POST', '/login')->with($credentials->all())->handle();

        // Then
        $this->assertToken($credentials->get('email'), json_decode((string) $response->getBody())->token);
    }

    /**
     * @test
     *
     * @expectedException HttpException
     */
    public function itShouldRequireThePasswordConfirmationOnRegisterUser()
    {
        $credentials = factory(UserFactory::class, ['password' => 'Jobsity@2022'])->make()->only(['email', 'password']);

        $response = $this->visit('POST', '/register')->with($credentials->all())->handle();

        $this->assertEquals('The password_confirmation field is required.', json_decode((string) $response->getBody())->message);
    }

    /**
     * @test
     *
     * @expectedException HttpException
     */
    public function itShouldCheckThePasswordWithThePasswordConfirmationOnRegisterUser()
    {
        $credentials = factory(UserFactory::class, ['password' => 'Jobsity@2022'])->make()->only(['email', 'password']);
        $credentials->put('password_confirmation', 'some other password');

        $response = $this->visit('POST', '/register')->with($credentials->all())->handle();

        $this->assertEquals('Password must match the confirmation.', json_decode((string) $response->getBody())->message);

    }

    /**
     * @test
     *
     * @expectedException HttpUnauthorizedException
     */
    public function itShouldNotAllowTheUserSignInWithFakeEmail()
    {
        $user = factory(UserFactory::class)->make();
        $fake_credentials = $user->put('email', 'jobsity@mail.com')->all();

        $response = $this->visit('POST', '/login')->with($fake_credentials)->handle();

        $this->assertEquals('Incorrect username or password.', json_decode((string) $response->getBody())->message);
    }

    /**
     * @test
     *
     * @expectedException HttpUnauthorizedException
     */
    public function itShouldNotAllowTheUserSignInWithWrongPassword()
    {
        $user = factory(UserFactory::class)->make();
        $fake_credentials = $user->put('password', 'Jobsity@2021')->all();

        $response = $this->visit('POST', '/login')->with($fake_credentials)->handle();

        $this->assertEquals('Incorrect username or password.', json_decode((string) $response->getBody())->message);
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
