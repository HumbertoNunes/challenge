<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends Controller
{
    /**
     * Retrives all users
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        return $this->asJson($response, User::all());
    }

    /**
     * Retrives the user
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->asJson($response, User::query()->find($args['user']));
    }

    /**
     * creates a new user
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
    	$params = (array) $request->getParsedBody();
        $params['password'] = password_hash($params['password'], PASSWORD_BCRYPT);

        $user = new User();
        $user->insert($params);

        return $this->asJson($response, $user, 201);
    }    
}