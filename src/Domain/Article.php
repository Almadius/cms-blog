<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class Article
{
    /**
     * @param list<Category> $categories
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $description,
        public readonly string $content,
        public readonly ?string $image,
        public readonly int $viewsCount,
        public readonly DateTimeImmutable $publishedAt,
        public readonly DateTimeImmutable $createdAt,
        public readonly array $categories = [],
    ) {
    }
}
