<?php

namespace Database\Factories;

use Illuminate\Support\Collection;
use \Faker\Generator as Faker;

trait Factory
{
    public function __construct(Faker $faker, array $attributes = [])
    {
        $this->faker = $faker;
        $this->collection = collect();
        $this->attributes = $attributes;
    }

    /**
     * Makes a mock of some model
     *
     * @param int $amount
     *
     * @return mixed
     */
    public function make(int $amount = 1)
    {
        for ($i=0; $i < $amount; $i++) { 
            $model = new parent($this->define());

            $this->collection->push($model);
        }

        return $amount > 1 ? $this->collection : $this->collection->first();
    }

    /**
     * Persist a mock in the database
     *
     * @param int $amount
     *
     * @return mixed
     */
    public function create(int $amount = 1)
    {
        $models = $this->make($amount);

        $values = $amount > 1 ?
             $models->map(fn($model) => collect($model)->only($this->fillable)->toArray()) :
             $models->only($this->fillable);

        query()->from($this->table)->insert($values->all());

        return $models;
    }

    /**
     * Define the attributes values for the mock instance
     *
     * @return array
     */
    abstract protected function define(): array;
}
