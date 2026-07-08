<article class="card">
    <a class="card__media" href="/article/{$article.slug}">
        <img src="/assets/uploads/{$article.image}" alt="{$article.title|escape}">
    </a>
    <h3 class="card__title"><a href="/article/{$article.slug}">{$article.title|escape}</a></h3>
    <div class="card__meta">{$article.published_at|nice_date}</div>
    <p class="card__excerpt">{$article.description|escape}</p>
    <a class="card__more" href="/article/{$article.slug}">Continue Reading</a>
</article>
