<?php

require 'vendor/autoload.php';

$dotenv = file_get_contents(__DIR__ . '/../.env');

$token = 'APP_KEY=base64:' . base64_encode(Faker\Factory::create()->bothify('?#??###?##?.??#?##?#?#?#???.#??#?#?'));

$dotenv = preg_replace('/APP_KEY=(.+)?/', $token, $dotenv);

file_put_contents(__DIR__ . '/../.env', $dotenv);

echo 'Application key generated' . PHP_EOL;
