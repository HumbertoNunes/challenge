<?php

namespace App\Models;

use Slim\Exception\HttpUnauthorizedException;
use Exception;

class Auth
{
    private const PASSWORD_LENGTH = 8;

    /**
     * Registers a new user
     *
     * @return \sdtClass
     */
    public static function register(string $email, string $password, string $password_confirmation)
    {
        if (strlen($password) < self::PASSWORD_LENGTH) {
            throw new Exception('Password must be at least ' . self::PASSWORD_LENGTH . ' characters.');
        }

        if ($password !== $password_confirmation) {
            throw new Exception('Password must match the confirmation.');
        }

        password_hash($password, PASSWORD_BCRYPT);

        return query()->from('users')->insert(compact('email', 'password'));
    }

    /**
     * Authenticate the user
     *
     * @return string
     */
    public static function login(array $credentials)
    {
        extract($credentials);

        $user = query()->from('users')->where('email', $email ?? '')->first();

        if (!$user || !password_verify($password ?? '', $user->password)) {
            throw new Exception("Incorrect username or password.");
        }

        return 'Bearer ' . base64_encode("{$email}:{$password}");
    }
}