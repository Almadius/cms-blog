{extends file="layout.tpl"}

{block name="content"}
<h1>Последние статьи по категориям</h1>

{if $categories|@count === 0}
    <p class="empty-state">Пока нет опубликованных статей.</p>
{else}
    {foreach $categories as $category}
        <section class="category-section">
            <div class="category-section__header">
                <h2>
                    <a href="/category/{$category.slug|escape}">{$category.name|escape}</a>
                </h2>
                {if $category.description}
                    <p class="category-section__description">{$category.description|escape}</p>
                {/if}
            </div>

            <div class="articles-grid">
                {foreach $category.articles as $article}
                    {include file="partials/article_card.tpl" article=$article}
                {/foreach}
            </div>

            <div class="category-section__footer">
                <a href="/category/{$category.slug|escape}" class="btn btn--outline">Все статьи</a>
            </div>
        </section>
    {/foreach}
{/if}
{/block}
