<article class="article-card">
    {if $article.image}
        <a href="/article/{$article.slug|escape}" class="article-card__image-link">
            <img src="{$article.image|escape}" alt="{$article.title|escape}" class="article-card__image">
        </a>
    {/if}
    <div class="article-card__body">
        <h3 class="article-card__title">
            <a href="/article/{$article.slug|escape}">{$article.title|escape}</a>
        </h3>
        <p class="article-card__description">{$article.description|escape}</p>
        <div class="article-card__meta">
            <time datetime="{$article.publishedAt|date_format:'%Y-%m-%d'}">
                {$article.publishedAt|date_format:'%d.%m.%Y'}
            </time>
            <span>{$article.viewsCount|escape} просм.</span>
        </div>
    </div>
</article>
