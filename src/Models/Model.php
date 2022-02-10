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
                if (!in_array($attribute, $this->fillable)) {
                    continue;
                }
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
        $model = new static();

        return query()->from($model->table);
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

    public function refresh()
    {
        $query = self::query();

        foreach ($this->fillable as $attribute) {
            $query->where($attribute, $this->$attribute);
        }

        $attributes = (array) $query->first();

        foreach ($attributes as $attribute => $value) {
            $this->$attribute = $value;
        }

        return $this;
    }
}
