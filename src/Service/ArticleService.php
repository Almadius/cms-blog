<?php

declare(strict_types=1);

namespace App\Service;

use App\Config;
use App\Domain\Article;
use App\Exception\NotFoundException;
use App\Repository\ArticleRepositoryInterface;

final class ArticleService
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly Config $config,
    ) {
    }

    /**
     * @return array{article: Article, similar: list<Article>}
     */
    public function getArticlePage(string $slugOrId): array
    {
        $article = $this->resolveArticle($slugOrId);
        if ($article === null) {
            throw new NotFoundException('Article not found');
        }

        $this->articleRepository->incrementViews($article->id);

        $categoryIds = array_map(static fn ($c) => $c->id, $article->categories);
        $limit = (int) $this->config->get('pagination.similar_articles_count', 3);

        $similar = $this->articleRepository->findSimilar($article->id, $categoryIds, $limit);

        $article = new Article(
            id: $article->id,
            title: $article->title,
            slug: $article->slug,
            description: $article->description,
            content: $article->content,
            image: $article->image,
            viewsCount: $article->viewsCount + 1,
            publishedAt: $article->publishedAt,
            createdAt: $article->createdAt,
            categories: $article->categories,
        );

        return [
            'article' => $article,
            'similar' => $similar,
        ];
    }

    private function resolveArticle(string $slugOrId): ?Article
    {
        if (ctype_digit($slugOrId)) {
            return $this->articleRepository->findById((int) $slugOrId);
        }

        return $this->articleRepository->findBySlug($slugOrId);
    }
}
