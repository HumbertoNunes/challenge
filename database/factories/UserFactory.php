<?php

namespace Database\Factories;

use App\Models\User;
use Database\Factories\Factory;

class UserFactory extends User
{
    use Factory;

    /**
     * Define the attributes values for the mock instance
     *
     * @return array
     */
    public function define(): array
    {
        extract($this->attributes ?? []); //Provides any specific attributes

        return [
            'email' => $email ?? $this->faker->email(),
            'password' => password_hash($password ?? $this->faker->bothify('?#?#??##?##?#??'), PASSWORD_BCRYPT),
        ];
    }
}
