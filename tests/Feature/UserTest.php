<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Slim\Exception\HttpUnauthorizedException;
use Tests\BaseTestCase;
use Tests\Helpers\RefreshDatabase;

class UserTest extends BaseTestCase
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
    public function itShouldRetrieveAllUsers()
    {
        factory(UserFactory::class)->create(4);

        $response = $this->login()->visit('GET', '/users')->handle();
        $users = json_decode((string) $response->getBody());

        $this->assertCount(5, $users);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
