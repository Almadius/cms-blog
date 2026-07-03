<?php

declare(strict_types=1);

return [
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'blog_cms',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
    ],
    'pagination' => [
        'articles_per_page' => (int) ($_ENV['ARTICLES_PER_PAGE'] ?? 10),
        'home_articles_per_category' => (int) ($_ENV['HOME_ARTICLES_PER_CATEGORY'] ?? 3),
        'similar_articles_count' => (int) ($_ENV['SIMILAR_ARTICLES_COUNT'] ?? 3),
    ],
    'smarty' => [
        'template_dir' => dirname(__DIR__) . '/templates',
        'compile_dir' => dirname(__DIR__) . '/templates_c',
        'cache_dir' => dirname(__DIR__) . '/cache',
    ],
];
