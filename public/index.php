<?php

declare(strict_types=1);

use App\Config;
use App\Container;
use App\Http\Controller\ArticleController;
use App\Http\Controller\CategoryController;
use App\Http\Controller\HomeController;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);

if (file_exists($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

$config = new Config(require $root . '/config/app.php');
$container = new Container($config);

$compileDir = $config->get('smarty.compile_dir');
$cacheDir = $config->get('smarty.cache_dir');

foreach ([$compileDir, $cacheDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$router = new Router();
$router->get('/', fn (Request $request): Response => $container->get(HomeController::class)->index($request));
$router->get('/category/{slugOrId}', fn (Request $request): Response => $container->get(CategoryController::class)->show($request));
$router->get('/article/{slugOrId}', fn (Request $request): Response => $container->get(ArticleController::class)->show($request));

$request = Request::fromGlobals();

try {
    $response = $router->dispatch($request);
} catch (Throwable $e) {
    if ($config->get('debug')) {
        throw $e;
    }

    error_log($e->getMessage());
    $response = Response::html('Internal Server Error', 500);
}

$response->send();
