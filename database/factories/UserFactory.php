<?php

namespace Database\Factories;

use App\Models\User;
use Database\Factories\Factory;
use Illuminate\Support\Collection;
use \Faker\Generator as Faker;

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
