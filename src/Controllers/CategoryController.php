<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\View;

class CategoryController
{
    private const PER_PAGE = 6;
    private const ALLOWED_SORTS = ['date', 'views'];

    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function show(string $slug): string
    {
        $category = Category::findBySlug($slug);

        if ($category === null) {
            http_response_code(404);

            return $this->view->render('not-found.tpl');
        }

        $sort = $_GET['sort'] ?? 'date';
        if (!in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'date';
        }

        $total = Article::countByCategory((int) $category['id']);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min($pages, (int) ($_GET['page'] ?? 1)));

        $articles = Article::paginateByCategory((int) $category['id'], $sort, $page, self::PER_PAGE);

        return $this->view->render('category.tpl', [
            'category' => $category,
            'articles' => $articles,
            'sort' => $sort,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
        ]);
    }
}
