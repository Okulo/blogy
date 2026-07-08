{extends file="layout.tpl"}

{block name="title"}{$category.name|escape} — Blogy{/block}

{block name="content"}
    <div class="category-header">
        <h1 class="category-header__title">{$category.name|escape}</h1>
        {if $category.description}
            <p class="category-header__desc">{$category.description|escape}</p>
        {/if}
    </div>

    <div class="toolbar">
        <span class="toolbar__label">Sort by</span>
        <a class="toolbar__link {if $sort == 'date'}is-active{/if}" href="/category/{$category.slug}?sort=date">Newest</a>
        <a class="toolbar__link {if $sort == 'views'}is-active{/if}" href="/category/{$category.slug}?sort=views">Most viewed</a>
    </div>

    {if $articles}
        <div class="grid">
            {foreach $articles as $article}
                {include file="partials/card.tpl" article=$article}
            {/foreach}
        </div>

        {include file="partials/pagination.tpl"}
    {else}
        <p class="empty">No articles in this category yet.</p>
    {/if}
{/block}
