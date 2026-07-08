<?php

use App\Controllers\ArticleController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Router;
use App\View;

require dirname(__DIR__) . '/vendor/autoload.php';

$view = new View();
$router = new Router();

$router->add('/', static fn (): string => (new HomeController($view))->index());
$router->add('/category/{slug}', static fn (array $params): string => (new CategoryController($view))->show($params['slug']));
$router->add('/article/{slug}', static fn (array $params): string => (new ArticleController($view))->show($params['slug']));

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

echo $router->dispatch($path);
