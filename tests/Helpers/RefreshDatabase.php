<?php

namespace Tests\Helpers;

trait RefreshDatabase
{
	public function refreshDatabase()
	{
		exec('composer migrate down -q');
		exec('composer migrate up -q');
	}
}