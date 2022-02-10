<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Log;
use Database\Factories\LogFactory;
use Database\Factories\UserFactory;
use Slim\Exception\HttpException;
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
     * test
     */
    public function itShouldRetrieveAStockQuote()
    {
        $stock_code = $this->randomStockCode();

        $response = $this->login()->visit('GET', '/stock')->with(['q' => $stock_code])->handle();
        $stockQuote = json_decode((string) $response->getBody(), true);

        $this->assertCount(6, $stockQuote);
        $this->assertEquals($stock_code, $stockQuote['symbol']);
    }

    /**
     * @test
     */
    public function itShouldNotRetrieveAQuoteGivenAFakeStockCode()
    {
        $stock_code = 'A' . $this->randomStockCode() . 'S';

        $response = $this->login()->visit('GET', '/stock')->with(['q' => $stock_code])->handle();
        $response = json_decode((string) $response->getBody());

        $this->assertEquals($stock_code . ' is not a valid stock code', $response->message);
    }

   /**
     * @test
     */
    public function itShouldCreateALogHistoryOfTheQueriesMadeToTheApiService()
    {
        $jane = factory(UserFactory::class)->create();
        $john = factory(UserFactory::class)->create();

        $apple_code = 'aapl.us';
        $airbnb_code = 'abnb.us';

        $this->login(['email' => $jane->email])->visit('GET', '/stock')->with(['q' => $apple_code])->handle();

        $this->login(['email' => $john->email])->visit('GET', '/stock')->with(['q' => $airbnb_code])->handle();
        $this->login(['email' => $john->email])->visit('GET', '/stock')->with(['q' => $airbnb_code])->handle();

        $this->assertCount(1, Log::query()->whereUserId($jane->id)->get());
        $this->assertCount(2, Log::query()->whereUserId($john->id)->get());
    }

    /**
     * @test
     */
    public function itShouldRetrieveAllRequestsMadeToTheApiService()
    {
        $user = factory(UserFactory::class)->create();

        $logs = factory(LogFactory::class, ['user_id' => $user->id])->create(10);

        $response = $this->login(['email' => $user->email])->visit('GET', '/history')->handle();

        $this->assertCount(10, json_decode((string) $response->getBody(), true));
    }

    /**
     * Returns a random stock code
     *
     * @return bool
     */
    private function randomStockCode()
    {
        return [
            'AAM_B.US',
            'AAN.US',
            'AAOI.US',
            'AAON.US',
            'AAP.US',
            'AAPL.US',
            'AAQC-U.US',
            'AAQC-WS.US',
            'AAQC.US',
            'AAT.US'
        ][random_int(0, 9)];
    }
}
