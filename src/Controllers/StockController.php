<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Log;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;
use Symfony\Component\DomCrawler\Crawler;

class StockController extends Controller
{
   /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function stock(Request $request, Response $response, array $args): Response
    {
        try {
            $stock = collect($request->getQueryParams());

            $this->validate($stock, ['q'], [
                'q' => 'The stock code is required and must be passed through the query string, e.g., /stock?q=abnb.us'
            ]);

            $this->validateStockCode($stock->get('q'));

            $client = new Client();

            $guzzleResponse = $client->request(
                'GET',
                'https://stooq.com/q/l/?s=' . $stock->get('q') . '&f=sd2t2ohlcvn&h&e=csv'
            );

            $stock_quote = $this->sanitize($guzzleResponse);

            Log::quote($stock_quote);

            // Mail::to(auth()->user->email)->send($stock_quote);

            $response->getBody()->write(json_encode($stock_quote));

            return $response;
        } catch (Exception $e) {
            throw new HttpException($request, $e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function history(Request $request, Response $response, array $args): Response
    {
        $logs = Log::query()->orderByDesc('date')->get();

        return $this->asJson($response, $logs);
    }

    /**
     * Sanitize the guzzle response to the needed format
     *
     * @param GuzzleResponse $guzzleResponse
     *
     * @return array
     */
    private function sanitize(GuzzleResponse $guzzleResponse): array
    {
        $content = trim($guzzleResponse->getBody()->getContents());

        $content = explode(
            ',',
            preg_replace('/Name\s+/', 'Name,', $content)
        );

        $stock_quote = array_change_key_case(
            array_combine(...array_chunk($content, count($content)/2))
        );

        extract($stock_quote);

        return compact('name', 'symbol', 'open', 'high', 'low', 'close');
    }

    /**
     * 
     */
    public function validateStockCode($stock_code)
    {
        if (!preg_match('/^(\D)+\.us$/i', $stock_code)) {
            throw new Exception("{$stock_code} is not a valid stock code", 400);
        }
    }
}
