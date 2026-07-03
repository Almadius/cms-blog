{extends file="layout.tpl"}

{block name="content"}
<article class="article-page">
    {if $article.image}
        <img src="{$article.image|escape}" alt="{$article.title|escape}" class="article-page__image">
    {/if}

    <header class="page-header">
        <h1>{$article.title|escape}</h1>
        <div class="article-meta">
            <time datetime="{$article.publishedAt|date_format:'%Y-%m-%d'}">
                {$article.publishedAt|date_format:'%d.%m.%Y'}
            </time>
            <span class="article-meta__views">{$article.viewsCount|escape} просмотров</span>
        </div>
        {if $article.categories|@count > 0}
            <div class="article-categories">
                {foreach $article.categories as $cat}
                    {include file="partials/category_badge.tpl" category=$cat}
                {/foreach}
            </div>
        {/if}
    </header>

    <p class="article-page__description">{$article.description|escape}</p>

    <div class="article-page__content">
        {$article.content|escape|nl2br}
    </div>

    {if $similar|@count > 0}
        <section class="similar-articles">
            <h2>Похожие статьи</h2>
            <div class="articles-grid">
                {foreach $similar as $sim}
                    {include file="partials/article_card.tpl" article=$sim}
                {/foreach}
            </div>
        </section>
    {/if}
</article>
{/block}
