{extends file="layout.tpl"}

{block name="title"}{$article.title|escape} — Blogy{/block}

{block name="content"}
    <article class="post">
        <div class="post__categories">
            {foreach $categories as $category}
                <a class="tag" href="/category/{$category.slug}">{$category.name|escape}</a>
            {/foreach}
        </div>

        <h1 class="post__title">{$article.title|escape}</h1>

        <div class="post__meta">
            <span>{$article.published_at|nice_date}</span>
            <span>{$article.views} views</span>
        </div>

        <img class="post__image" src="/assets/uploads/{$article.image}" alt="{$article.title|escape}">

        <p class="post__lead">{$article.description|escape}</p>

        <div class="post__body">{$article.body|escape|nl2br}</div>
    </article>

    {if $similar}
        <section class="related">
            <h2 class="related__title">Similar articles</h2>
            <div class="grid">
                {foreach $similar as $article}
                    {include file="partials/card.tpl" article=$article}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
