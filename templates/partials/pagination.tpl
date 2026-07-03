{if $pagination.total_pages > 1}
<nav class="pagination" aria-label="Пагинация">
    {if $pagination.current_page > 1}
        <a href="{$base_url}?sort={$pagination.sort|escape}&order={$pagination.order|escape}&page={$pagination.current_page - 1}"
           class="pagination__link">&laquo; Назад</a>
    {/if}

    <span class="pagination__info">
        Страница {$pagination.current_page|escape} из {$pagination.total_pages|escape}
    </span>

    {if $pagination.current_page < $pagination.total_pages}
        <a href="{$base_url}?sort={$pagination.sort|escape}&order={$pagination.order|escape}&page={$pagination.current_page + 1}"
           class="pagination__link">Вперёд &raquo;</a>
    {/if}
</nav>
{/if}
