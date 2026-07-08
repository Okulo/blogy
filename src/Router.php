<?php

namespace App;

class Router
{
    private array $routes = [];

    public function add(string $pattern, callable $handler): void
    {
        $this->routes[$pattern] = $handler;
    }

    public function dispatch(string $path): string
    {
        foreach ($this->routes as $pattern => $handler) {
            $regex = '#^' . preg_replace('#\{([a-z]+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';

            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return $handler($params);
            }
        }

        http_response_code(404);

        return $this->notFound();
    }

    private function notFound(): string
    {
        return '<!doctype html><meta charset="utf-8"><title>Not found</title>'
            . '<p style="font-family:sans-serif;padding:40px">Page not found. '
            . '<a href="/">Back to home</a></p>';
    }
}
