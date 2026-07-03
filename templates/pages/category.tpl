{extends file="layout.tpl"}

{block name="content"}
<article class="category-page">
    <header class="page-header">
        <h1>{$category.name|escape}</h1>
        {if $category.description}
            <p class="page-header__description">{$category.description|escape}</p>
        {/if}
    </header>

    <div class="sort-controls">
        <span>Сортировка:</span>
        <a href="/category/{$category.slug|escape}?sort=date&order=desc"
           class="sort-link{if $pagination.sort == 'date' && $pagination.order == 'desc'} is-active{/if}">По дате ↓</a>
        <a href="/category/{$category.slug|escape}?sort=date&order=asc"
           class="sort-link{if $pagination.sort == 'date' && $pagination.order == 'asc'} is-active{/if}">По дате ↑</a>
        <a href="/category/{$category.slug|escape}?sort=views&order=desc"
           class="sort-link{if $pagination.sort == 'views' && $pagination.order == 'desc'} is-active{/if}">По просмотрам ↓</a>
        <a href="/category/{$category.slug|escape}?sort=views&order=asc"
           class="sort-link{if $pagination.sort == 'views' && $pagination.order == 'asc'} is-active{/if}">По просмотрам ↑</a>
    </div>

    {if $articles|@count === 0}
        <p class="empty-state">В этой категории пока нет статей.</p>
    {else}
        <div class="articles-grid">
            {foreach $articles as $article}
                {include file="partials/article_card.tpl" article=$article}
            {/foreach}
        </div>

        {include file="partials/pagination.tpl"
            base_url="/category/{$category.slug|escape}"
            pagination=$pagination}
    {/if}
</article>
{/block}
