<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Article;

interface ArticleRepositoryInterface
{
    /**
     * Latest articles for a category (home page block).
     *
     * @return list<Article>
     */
    public function findLatestByCategory(int $categoryId, int $limit): array;

    /**
     * Paginated articles for category with sorting.
     *
     * @return list<Article>
     */
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

    /**
     * Similar articles sharing categories with the given article.
     *
     * @param list<int> $categoryIds
     * @return list<Article>
     */
    public function findSimilar(int $articleId, array $categoryIds, int $limit): array;
}
