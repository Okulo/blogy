<?php

namespace App\Controllers;

use App\Models\Article;
use App\View;

class ArticleController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function show(string $slug): string
    {
        $article = Article::findBySlug($slug);

        if ($article === null) {
            http_response_code(404);

            return $this->view->render('not-found.tpl');
        }

        Article::incrementViews((int) $article['id']);
        $article['views'] = (int) $article['views'] + 1;

        $categories = Article::categoriesOf((int) $article['id']);
        $categoryIds = array_map(static fn (array $category): int => (int) $category['id'], $categories);

        return $this->view->render('article.tpl', [
            'article' => $article,
            'categories' => $categories,
            'similar' => Article::similar((int) $article['id'], $categoryIds, 3),
        ]);
    }
}
