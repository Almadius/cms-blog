<?php

declare(strict_types=1);

namespace App\Service;

use App\Config;
use App\Domain\Category;
use App\Exception\NotFoundException;
use App\Repository\ArticleRepositoryInterface;
use App\Repository\CategoryRepositoryInterface;

final class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly Config $config,
    ) {
    }

    public function getHomePageData(): array
    {
        $limit = (int) $this->config->get('pagination.home_articles_per_category', 3);
        $categories = $this->categoryRepository->findWithArticles();

        return array_map(function (Category $category) use ($limit): Category {
            $articles = $this->articleRepository->findLatestByCategory($category->id, $limit);

            return new Category(
                id: $category->id,
                name: $category->name,
                slug: $category->slug,
                description: $category->description,
                createdAt: $category->createdAt,
                articles: $articles,
            );
        }, $categories);
    }

    public function getCategoryPage(string $slugOrId, string $sort, string $order, int $page): array
    {
        $category = $this->resolveCategory($slugOrId);
        if ($category === null) {
            throw new NotFoundException('Category not found');
        }

        $perPage = (int) $this->config->get('pagination.articles_per_page', 10);
        $sortField = in_array($sort, ['views', 'date'], true) ? $sort : 'date';
        $sortOrder = strtolower($order) === 'asc' ? 'asc' : 'desc';
        $total = $this->articleRepository->countByCategory($category->id);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $totalPages);
        $offset = ($page - 1) * $perPage;

        $articles = $this->articleRepository->findByCategory(
            $category->id,
            $sortField,
            $sortOrder,
            $perPage,
            $offset,
        );

        return [
            'category' => $category,
            'articles' => $articles,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $total,
                'per_page' => $perPage,
                'sort' => $sortField,
                'order' => $sortOrder,
            ],
        ];
    }

    private function resolveCategory(string $slugOrId): ?Category
    {
        if (ctype_digit($slugOrId)) {
            return $this->categoryRepository->findById((int) $slugOrId);
        }

        return $this->categoryRepository->findBySlug($slugOrId);
    }
}
