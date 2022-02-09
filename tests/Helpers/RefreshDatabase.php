<?php

namespace Tests\Helpers;

trait RefreshDatabase
{
    public function refreshDatabase()
    {
        $driver = $_ENV['DATABASE_DRIVER'];

        exec("composer migrate down driver:{$driver} -q");
        exec("composer migrate up driver:{$driver} -q");
    }
}
