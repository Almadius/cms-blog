<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Article;

interface ArticleRepositoryInterface
{
    public function findLatestByCategory(int $categoryId, int $limit): array;

    public function findByCategory(
        int $categoryId,
        string $sortField,
        string $sortOrder,
        int $limit,
        int $offset,
    ): array;

    public function countByCategory(int $categoryId): int;

    public function findBySlug(string $slug): ?Article;

    public function findById(int $id): ?Article;

    public function incrementViews(int $articleId): void;

    public function findSimilar(int $articleId, array $categoryIds, int $limit): array;
}
