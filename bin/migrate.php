<?php

declare(strict_types=1);

use App\Config;
use App\Database\Connection;
use App\Database\Migrator;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);

if (file_exists($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

$config = new Config(require $root . '/config/app.php');
$pdo = Connection::get($config);
$migrator = new Migrator($pdo, $root . '/migrations');
$migrator->run();

echo "Migrations completed.\n";
