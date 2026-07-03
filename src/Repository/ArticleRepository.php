<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Article;
use App\Domain\Category;
use DateTimeImmutable;
use PDO;

final class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findLatestByCategory(int $categoryId, int $limit): array
    {
        $sql = <<<'SQL'
            SELECT a.id, a.title, a.slug, a.description, a.content, a.image,
                   a.views_count, a.published_at, a.created_at
            FROM articles a
            INNER JOIN article_category ac ON ac.article_id = a.id
            WHERE ac.category_id = :category_id
            ORDER BY a.published_at DESC
            LIMIT :limit
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $this->hydrateArticles($stmt->fetchAll());
    }

    public function findByCategory(
        int $categoryId,
        string $sortField,
        string $sortOrder,
        int $limit,
        int $offset,
    ): array {
        $column = $sortField === 'views' ? 'a.views_count' : 'a.published_at';
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $sql = sprintf(
            'SELECT a.id, a.title, a.slug, a.description, a.content, a.image,
                    a.views_count, a.published_at, a.created_at
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :category_id
             ORDER BY %s %s
             LIMIT :limit OFFSET :offset',
            $column,
            $order,
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $this->hydrateArticles($stmt->fetchAll());
    }

    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM article_category WHERE category_id = :category_id',
        );
        $stmt->execute(['category_id' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    public function findBySlug(string $slug): ?Article
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, description, content, image,
                    views_count, published_at, created_at
             FROM articles WHERE slug = :slug LIMIT 1',
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrateArticle($row, $this->loadCategories((int) $row['id']));
    }

    public function findById(int $id): ?Article
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, description, content, image,
                    views_count, published_at, created_at
             FROM articles WHERE id = :id LIMIT 1',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrateArticle($row, $this->loadCategories($id));
    }

    public function incrementViews(int $articleId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE articles SET views_count = views_count + 1 WHERE id = :id',
        );
        $stmt->execute(['id' => $articleId]);
    }

    public function findSimilar(int $articleId, array $categoryIds, int $limit): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $sql = sprintf(
            'SELECT DISTINCT a.id, a.title, a.slug, a.description, a.content, a.image,
                    a.views_count, a.published_at, a.created_at,
                    COUNT(ac.category_id) AS match_count
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id IN (%s) AND a.id != ?
             GROUP BY a.id
             ORDER BY match_count DESC, a.published_at DESC
             LIMIT ?',
            $placeholders,
        );

        $params = [...$categoryIds, $articleId, $limit];
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $i => $value) {
            $stmt->bindValue($i + 1, $value, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $this->hydrateArticles($stmt->fetchAll());
    }

    /**
     * @return list<Category>
     */
    private function loadCategories(int $articleId): array
    {
        $sql = <<<'SQL'
            SELECT c.id, c.name, c.slug, c.description, c.created_at
            FROM categories c
            INNER JOIN article_category ac ON ac.category_id = c.id
            WHERE ac.article_id = :article_id
            ORDER BY c.name ASC
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['article_id' => $articleId]);
        $rows = $stmt->fetchAll();

        return array_map(static function (array $row): Category {
            return new Category(
                id: (int) $row['id'],
                name: $row['name'],
                slug: $row['slug'],
                description: $row['description'],
                createdAt: new DateTimeImmutable($row['created_at']),
            );
        }, $rows);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<Article>
     */
    private function hydrateArticles(array $rows): array
    {
        return array_map(fn (array $row): Article => $this->hydrateArticle($row), $rows);
    }

    /**
     * @param array<string, mixed> $row
     * @param list<Category> $categories
     */
    private function hydrateArticle(array $row, array $categories = []): Article
    {
        return new Article(
            id: (int) $row['id'],
            title: $row['title'],
            slug: $row['slug'],
            description: $row['description'],
            content: $row['content'],
            image: $row['image'],
            viewsCount: (int) $row['views_count'],
            publishedAt: new DateTimeImmutable($row['published_at']),
            createdAt: new DateTimeImmutable($row['created_at']),
            categories: $categories,
        );
    }
}
