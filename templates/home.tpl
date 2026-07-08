{extends file="layout.tpl"}

{block name="content"}
    {foreach $sections as $section}
        <section class="category-block">
            <div class="category-block__head">
                <h2 class="category-block__title">{$section.category.name|escape}</h2>
                <a class="category-block__all" href="/category/{$section.category.slug}">View All</a>
            </div>

            <div class="grid">
                {foreach $section.articles as $article}
                    {include file="partials/card.tpl" article=$article}
                {/foreach}
            </div>
        </section>
    {foreachelse}
        <p class="empty">No published articles yet.</p>
    {/foreach}
{/block}
