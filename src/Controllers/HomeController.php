<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\View;

class HomeController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function index(): string
    {
        $sections = [];

        foreach (Category::withArticles() as $category) {
            $sections[] = [
                'category' => $category,
                'articles' => Article::latestByCategory((int) $category['id'], 3),
            ];
        }

        return $this->view->render('home.tpl', ['sections' => $sections]);
    }
}
