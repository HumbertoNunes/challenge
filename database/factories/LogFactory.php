<?php

namespace Database\Factories;

use App\Models\Log;
use Database\Factories\Factory;

class LogFactory extends Log
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

        $symbol = $symbol ?? $this->faker->randomElement([
            'AAON.US',
            'AAPL.US',
            'ABEV.US',
            'ABNB.US'
        ]);

        $name = $name ?? [
            'AAON.US' => 'AAON',
            'AAPL.US' => 'APPLE',
            'ABEV.US' => 'AMBEV',
            'ABNB.US' => 'AIRBNB',
        ][$symbol];

        return [
            'user_id' => $user_id ?? factory(\Database\Factories\UserFactory::class)->create()->id,
            'date' => $date ?? $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'name' => $name,
            'symbol' => $symbol,
            'open' => $open ?? $this->faker->randomFloat(2, 20, 30),
            'high' => $high ?? $this->faker->randomFloat(2, 20, 30),
            'low' => $low ?? $this->faker->randomFloat(2, 20, 30),
            'close' => $close ?? $this->faker->randomFloat(2, 20, 30),
        ];
    }
}
