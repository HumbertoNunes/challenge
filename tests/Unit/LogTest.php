<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Log;
use Database\Factories\LogFactory;
use Database\Factories\UserFactory;
use Tests\BaseTestCase;
use Tests\Helpers\RefreshDatabase;

class LogTest extends BaseTestCase
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
    public function itShouldRegisterStockQuotes()
    {
        $user = factory(UserFactory::class)->create();
        $logs = factory(LogFactory::class, ['user_id' => $user->id])->make(20);

        $logs->each(fn ($log) => Log::quote((array) $log));

        $this->assertCount(20, Log::all());
    }
}
