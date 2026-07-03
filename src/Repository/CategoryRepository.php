<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Category;
use DateTimeImmutable;
use PDO;

final class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findWithArticles(): array
    {
        $sql = <<<'SQL'
            SELECT DISTINCT c.id, c.name, c.slug, c.description, c.created_at
            FROM categories c
            INNER JOIN article_category ac ON ac.category_id = c.id
            ORDER BY c.name ASC
        SQL;

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        return array_map($this->hydrate(...), $rows);
    }

    public function findBySlug(string $slug): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, slug, description, created_at FROM categories WHERE slug = :slug LIMIT 1',
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, slug, description, created_at FROM categories WHERE id = :id LIMIT 1',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    private function hydrate(array $row): Category
    {
        return new Category(
            id: (int) $row['id'],
            name: $row['name'],
            slug: $row['slug'],
            description: $row['description'],
            createdAt: new DateTimeImmutable($row['created_at']),
        );
    }
}
