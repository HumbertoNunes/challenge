<?php

namespace App\Models;

use Exception;
use Firebase\JWT\JWT;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Request;

class Auth
{
    private const PASSWORD_LENGTH = 8;

    /**
     * Registers a new user
     *
     * @param string $email
     * @param string $password
     * @param string $password_confirmation
     *
     * @return \sdtClass
     * @throws Exception
     */
    public static function register(string $email, string $password, string $password_confirmation)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('The email must be a valid email address.', 400);
        }

        $user = query()->from('users')->whereEmail($email)->first();

        if ($user) {
            throw new Exception('The email has already been taken.', 400);
        }

        if (strlen($password) < self::PASSWORD_LENGTH) {
            throw new Exception('Password must be at least ' . self::PASSWORD_LENGTH . ' characters.', 400);
        }

        if ($password !== $password_confirmation) {
            throw new Exception('Password must match the confirmation.', 400);
        }

        $password = password_hash($password, PASSWORD_BCRYPT);

        return query()->from('users')->insert(compact('email', 'password'));
    }

    /**
     * Authenticate the user
     *
     * @param string $email
     * @param string $password
     * @param string $password_confirmation
     *
     * @return string
     * @throws Exception
     */
    public static function login(string $email, string $password)
    {
        $user = query()->from('users')->whereEmail($email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            throw new Exception("Incorrect username or password.");
        }

        return JWT::encode($email, $_ENV['APP_KEY'], 'HS256');
    }

    /**
     * Checks if the user is a guest
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function isGuest()
    {
        return !auth();
    }
}