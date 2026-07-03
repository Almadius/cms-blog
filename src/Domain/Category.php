<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class Category
{
    /**
     * @param list<Article> $articles
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly DateTimeImmutable $createdAt,
        public readonly array $articles = [],
    ) {
    }
}
