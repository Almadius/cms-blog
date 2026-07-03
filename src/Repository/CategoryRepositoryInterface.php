<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Category;

interface CategoryRepositoryInterface
{
    /**
     * Categories that have at least one article.
     *
     * @return list<Category>
     */
    public function findWithArticles(): array;

    public function findBySlug(string $slug): ?Category;

    public function findById(int $id): ?Category;
}
