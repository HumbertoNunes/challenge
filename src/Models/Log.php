<?php

namespace App\Models;

use App\Models\Model;
use App\Models\User;

class Log extends Model
{
	protected string $table = 'logs';

	protected array $fillable = ['user_id', 'date', 'name', 'symbol', 'open', 'high', 'low', 'close'];

	public function __construct(array $attributes = null)
	{
		parent::__construct($attributes);
	}

	/**
	 * Return the user that created the log
	 */
	public function user()
	{
		return query()->from($this->table)->whereUserId($this->user_id)->get();
	}

	/**
	 * Creates a new log for the stock quote
	 *
	 * @param array $attributes
	 *
	 * @return bool
	 */
    public static function quote(array $attributes = null)
    {
        $model = new static($attributes);

        return query()->from($model->table)->insert((array) $model->only($model->fillable)->all());
    }
}