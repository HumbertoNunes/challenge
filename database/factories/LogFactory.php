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

        $symbol = $this->faker->randomElement([
            'AAON.US',
            'AAPL.US',
            'ABEV.US',
            'ABNB.US'
        ]);

        $name = [
            'AAON.US' => 'AAON',
            'AAPL.US' => 'APPLE',
            'ABEV.US' => 'AMBEV',
            'ABNB.US' => 'AIRBNB',
        ][$symbol];

        return [
            'user_id' => factory(\Database\Factories\UserFactory::class)->create()->id,
            'date' => $this->faker->dateTimeThisMonth(),
            'name' => $name,
            'symbol' => $symbol,
            'open' => $this->faker->randomFloat(2, 20, 30),
            'high' => $this->faker->randomFloat(2, 20, 30),
            'low' => $this->faker->randomFloat(2, 20, 30),
            'close' => $this->faker->randomFloat(2, 20, 30),
        ];
    }
}
