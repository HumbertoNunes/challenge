<?php

namespace App\Models;

use App\Models\Model;

class User extends Model
{
	protected string $table = 'users';

	protected array $fillable = ['email', 'password'];

	public function logs()
	{
		return $this->hasMany(Log::class);
	}
}