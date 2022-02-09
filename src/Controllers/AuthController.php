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
            $this->validate($params, ['email', 'password', 'password_confirmation']);

            Auth::register($params->get('email'), $params->get('password'), $params->get('password_confirmation'));

            return $this->asJson($response, 'User created', 201);
        } catch (QueryException $e) {
            throw new Exception($e->getMessage());
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
    public function login(Request $request, Response $response): Response
    {
        if (!Auth::isGuest($request)) {
            return $response->withHeader('Location', 'http://localhost:8080/history')->withStatus(302);
        }

        $params = collect((array) $request->getParsedBody());

        $this->validate($params, ['email', 'password']);

        try {
           $token = Auth::login($params->get('email'), $params->get('password'));

           return $this->asJson($response, $token, 200);
       } catch (Exception $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
       }
    }
}
