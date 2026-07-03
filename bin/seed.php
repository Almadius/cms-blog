<?php

declare(strict_types=1);

use App\Config;
use App\Database\Connection;
use App\Database\Seeder;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);

if (file_exists($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

$config = new Config(require $root . '/config/app.php');
$pdo = Connection::get($config);
$seeder = new Seeder($pdo);
$seeder->run();
