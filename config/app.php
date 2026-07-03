<?php

declare(strict_types=1);

$env = static function (string $key, mixed $default = null): mixed {
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    $value = getenv($key);

    return $value !== false ? $value : $default;
};

return [
    'env' => $env('APP_ENV', 'production'),
    'debug' => filter_var($env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
    'db' => [
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => (int) $env('DB_PORT', 3306),
        'name' => $env('DB_NAME', 'blog_cms'),
        'user' => $env('DB_USER', 'root'),
        'password' => $env('DB_PASSWORD', ''),
    ],
    'pagination' => [
        'articles_per_page' => (int) $env('ARTICLES_PER_PAGE', 10),
        'home_articles_per_category' => (int) $env('HOME_ARTICLES_PER_CATEGORY', 3),
        'similar_articles_count' => (int) $env('SIMILAR_ARTICLES_COUNT', 3),
    ],
    'smarty' => [
        'template_dir' => dirname(__DIR__) . '/templates',
        'compile_dir' => dirname(__DIR__) . '/templates_c',
        'cache_dir' => dirname(__DIR__) . '/cache',
    ],
];
