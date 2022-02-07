<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Auth;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;
use Slim\Exception\HttpUnauthorizedException;

class AuthController extends Controller
{
   /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function register(Request $request, Response $response): Response
    {
        $params = collect((array) $request->getParsedBody());

        try {
            collect(['email', 'password', 'password_confirmation'])
            ->each(function ($item) use ($params) {
                if(!$params->has($item)) {
                    throw new Exception("The {$item} field is required.", 400);
                }
            });

            Auth::register($params->get('email'), $params->get('password'), $params->get('password_confirmation'));

            return $this->asJson($response, 'User created', 201);
        } catch (Exception $e) {
            throw new HttpException($request, $e->getMessage(), $e->getCode());
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function login(Request $request, Response $response): Response
    {
        $params = (array) $request->getParsedBody();

        // dd($request->getHeaders()['Authorization'][0]);

        try {
           $token = Auth::login($params);

           return $this->asJson($response, $token, 200);
       } catch (Exception $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
       }
    }
}
