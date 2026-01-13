<?php

/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */
declare(strict_types=1);

namespace Modestox\Core;

/**
 * Class Router
 * Handles URI matching against routes registered by modules.
 */
class Router
{
    private static array $routes = [];
    private string $uri;

    public function __construct()
    {
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    }

    /**
     * Registers a new route into the global registry.
     */
    public static function add(string $path, string $controller, string $method = 'index'): void
    {
        self::$routes[trim($path, '/')] = [
            'controller' => $controller,
            'method'     => $method,
        ];
    }

    /**
     * Resolves the current request.
     */
    public function resolve(): void
    {
        $path = trim($this->uri, '/');

        if (isset(self::$routes[$path])) {
            $route = self::$routes[$path];
            $class = $route['controller'];
            $method = $route['method'];

            if (class_exists($class)) {
                (new $class())->$method();
                return;
            }
        }

        $this->notFound();
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
    }
}