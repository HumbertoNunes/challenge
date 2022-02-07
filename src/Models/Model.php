<?php

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class Model
{
    protected string $table;

    public function __construct(array $attributes = null)
    {
        if ($attributes) {
            foreach ($attributes as $attribute => $value) {
                if (!in_array($attribute, $this->fillable)) continue;
                $this->$attribute = $value;
            }
        }
    }

    public function __call($method, $arguments)
    {
        if (in_array($method, get_class_methods(collect()))) {
            return collect((array) $this)->$method(...$arguments);
        }
    }

    /**
     * @return Builder
     */
    public static function query(): Builder
    {
        return query();
    }

    /**
     * Returns a collection with all users
     *
     * @return Collection
     */
    public static function all(): Collection
    {
        $model = new static();

        return query()->from($model->table)->get();
    }
}