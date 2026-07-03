# Blog CMS

Простой блог-движок на чистом PHP 8.1+ без фреймворков: Smarty, MySQL, слоистая архитектура (Repository + Service + Controller).

## Возможности

- **Главная** — категории с хотя бы одной статьёй, по 3 последних статьи в каждой
- **Категория** (`/category/{slug}`) — список статей, сортировка (`?sort=views|date&order=asc|desc`), пагинация (`?page=N`)
- **Статья** (`/article/{slug}`) — полный контент, инкремент просмотров (`UPDATE ... views_count + 1`), похожие статьи

## Требования

- PHP 8.1+
- Composer
- MySQL 8+
- Node.js (опционально, для компиляции SCSS)

## Быстрый старт (Docker)

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/migrate.php
docker compose exec php php bin/seed.php
```

Пересоздать демо-данные:

```bash
docker compose exec php php bin/seed.php --force
```

Откройте http://localhost:8080

> MySQL пробрасывается на хост-порт **3307** (чтобы не конфликтовать с локальным MySQL на 3306). Внутри Docker PHP подключается к `mysql:3306` автоматически.

## Локальная установка

```bash
composer install
cp .env.example .env
# Настройте DB_* в .env и создайте базу blog_cms
php bin/migrate.php
php bin/seed.php
php -S localhost:8000 -t public
```

Откройте http://localhost:8000

## SCSS

```bash
npm install
npm run build:css    # однократная компиляция
npm run watch:css    # watch-режим
```

## Структура

```
public/          — front controller (index.php)
src/Http/        — Router, Request, Response, Controllers
src/Domain/      — Entity (Category, Article)
src/Repository/  — PDO-репозитории (интерфейсы + реализации)
src/Service/     — бизнес-логика
src/Database/    — Connection, Migrator, Seeder
templates/       — Smarty-шаблоны
migrations/      — SQL-миграции
bin/             — CLI: migrate.php, seed.php
```

## Лицензия

MIT
