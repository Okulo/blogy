{if $pages > 1}
    <nav class="pagination">
        {if $page > 1}
            <a class="pagination__item" href="/category/{$category.slug}?sort={$sort}&page={$page-1}">Prev</a>
        {/if}

        {for $n=1 to $pages}
            <a class="pagination__item {if $n == $page}is-active{/if}" href="/category/{$category.slug}?sort={$sort}&page={$n}">{$n}</a>
        {/for}

        {if $page < $pages}
            <a class="pagination__item" href="/category/{$category.slug}?sort={$sort}&page={$page+1}">Next</a>
        {/if}
    </nav>
{/if}
