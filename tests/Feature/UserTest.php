<?php

declare(strict_types=1);

namespace Tests\Feature;

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
    public function it_should_retrieve_all_users()
    {
        $factory_users = factory(UserFactory::class)->create(5);

        $response = $this->visit('GET', '/users')->handle();
        $users = json_decode((string) $response->getBody());

        foreach ($users as $key => $user) {
            $this->assertEquals($factory_users->get($key)->email, $user->email);
        }

        $this->assertCount(5, $users);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
