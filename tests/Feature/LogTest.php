<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Log;
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
     * @test
     */
    public function it_should_retrieve_a_stock_quote()
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
    public function it_should_not_retrieve_a_quote_given_a_fake_stock_code()
    {
        $stock_code = 'A' . $this->randomStockCode() . 'S';

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("{$stock_code} is not a valid stock code");

        $response = $this->login()->visit('GET', '/stock')->with(['q' => $stock_code])->handle();
        $stockQuote = json_decode((string) $response->getBody(), true);
    }

   /**
     * @test
     */
    public function it_should_create_a_log_history_of_the_queries_made_to_the_api_service()
    {
        $apple_code = 'aapl.us';
        $airbnb_code = 'abnb.us';

        $this->login()->visit('GET', '/stock')->with(['q' => $apple_code])->handle();

        $this->login()->visit('GET', '/stock')->with(['q' => $airbnb_code])->handle();
        $this->login()->visit('GET', '/stock')->with(['q' => $airbnb_code])->handle();

        $this->assertCount(1, Log::query()->where('symbol', 'aapl.us')->get());
        $this->assertCount(2, Log::query()->where('symbol', 'abnb.us')->get());
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
