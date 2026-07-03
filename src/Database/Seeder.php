<?php

declare(strict_types=1);

namespace App\Database;

use Faker\Factory;
use Faker\Generator;
use PDO;

/**
 * Populates database with demo categories and articles (CLI only).
 */
final class Seeder
{
    private readonly Generator $faker;

    public function __construct(
        private readonly PDO $pdo,
    ) {
        $this->faker = Factory::create('ru_RU');
    }

    public function run(): void
    {
        if ($this->hasData()) {
            echo "Database already contains data. Skipping seed.\n";

            return;
        }

        $categoryIds = $this->seedCategories();
        $this->seedArticles($categoryIds);

        echo "Seeding completed.\n";
    }

    private function hasData(): bool
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() > 0;
    }

    /**
     * @return list<int>
     */
    private function seedCategories(): array
    {
        $categories = [
            ['name' => 'Технологии', 'description' => 'Новости и обзоры из мира IT'],
            ['name' => 'Путешествия', 'description' => 'Маршруты, советы и впечатления'],
            ['name' => 'Кулинария', 'description' => 'Рецепты и кулинарные лайфхаки'],
            ['name' => 'Спорт', 'description' => 'Соревнования, тренировки и здоровый образ жизни'],
            ['name' => 'Книги', 'description' => 'Рецензии и подборки для чтения'],
        ];

        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (name, slug, description, created_at) VALUES (:name, :slug, :description, :created_at)',
        );

        $ids = [];

        foreach ($categories as $category) {
            $slug = $this->slugify($category['name']);
            $stmt->execute([
                'name' => $category['name'],
                'slug' => $slug,
                'description' => $category['description'],
                'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            ]);
            $ids[] = (int) $this->pdo->lastInsertId();
        }

        return $ids;
    }

    /**
     * @param list<int> $categoryIds
     */
    private function seedArticles(array $categoryIds): void
    {
        $articleStmt = $this->pdo->prepare(
            'INSERT INTO articles (title, slug, description, content, image, views_count, published_at, created_at)
             VALUES (:title, :slug, :description, :content, :image, :views_count, :published_at, :created_at)',
        );

        $linkStmt = $this->pdo->prepare(
            'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)',
        );

        for ($i = 0; $i < 40; $i++) {
            $title = $this->faker->sentence(6);
            $slug = $this->uniqueSlug($this->slugify($title));
            $publishedAt = $this->faker->dateTimeBetween('-6 months', 'now');

            $articleStmt->execute([
                'title' => rtrim($title, '.'),
                'slug' => $slug,
                'description' => $this->faker->paragraph(2),
                'content' => implode("\n\n", $this->faker->paragraphs(5)),
                'image' => 'https://picsum.photos/seed/' . $slug . '/800/450',
                'views_count' => $this->faker->numberBetween(0, 5000),
                'published_at' => $publishedAt->format('Y-m-d H:i:s'),
                'created_at' => $publishedAt->format('Y-m-d H:i:s'),
            ]);

            $articleId = (int) $this->pdo->lastInsertId();
            $assigned = $this->faker->randomElements($categoryIds, $this->faker->numberBetween(1, 2));

            foreach ($assigned as $categoryId) {
                $linkStmt->execute([
                    'article_id' => $articleId,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $suffix = 1;

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM articles WHERE slug = :slug');

        while (true) {
            $stmt->execute(['slug' => $slug]);
            if ((int) $stmt->fetchColumn() === 0) {
                return $slug;
            }
            $slug = $base . '-' . $suffix++;
        }
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text) ?? '';
        $text = preg_replace('/[\s-]+/u', '-', trim($text)) ?? '';

        return $text !== '' ? $text : 'item';
    }
}
