<?php

namespace App\Controllers;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface as Response;
use \Exception;

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

    /**
     * Validates if the request contains all the required fields
     *
     * @param Collection $fields
     * @param array $required_fields
     *
     * @return void
     * @throws Exception
     */
    public function validate(Collection $fields, array $required_fields, array $messages = null)
    {
        collect($required_fields)
        ->each(function ($required_field) use ($fields, $messages) {
            if(!$fields->has($required_field) || empty($fields->get($required_field))) {
                $message = $messages[$required_field] ?? "The {$required_field} field is required.";
                throw new Exception($message, 400);
            }
        });
    }
}