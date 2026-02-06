<?php

declare(strict_types=1);

namespace Core\Routing;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $cachePath = dirname(__DIR__, 2) . '/var/cache/routes.php';
        if (file_exists($cachePath)) {
            $this->routes = require $cachePath;
        }
    }

    public function resolve(string $uri): ?string
    {
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');
        return $this->routes[$path] ?? null;
    }
}