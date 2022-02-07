<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class Controller
{
	/**
	 * Returns a JSON data with status
	 *
	 * @param Response $response
	 */
	public function asJson(Response $response, $payload, $statusCode = 200)
	{
		$response->getBody()->write(is_string($payload) ? $payload : json_encode($payload));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
	}
}