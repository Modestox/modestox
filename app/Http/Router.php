<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Http;

/**
 * Class Router
 * Responsible for matching URIs to controllers and managing the route cache.
 */
class Router
{
    /** @var array<string, string> List of routes: [path => ControllerClass] */
    private array $routes = [];

    /** @var string Full path to the compiled routes file */
    private readonly string $cacheFile;

    public function __construct()
    {
        // Define the cache file location relative to the project root
        $this->cacheFile = dirname(__DIR__, 2) . '/var/cache/routes.php';
        $this->loadRoutes();
    }

    /**
     * Resolves the URI to a controller class name.
     */
    public function resolve(string $uri): ?string
    {
        $uri = trim($uri, '/');
        return $this->routes[$uri] ?? null;
    }

    /**
     * Checks if the router has any routes registered.
     */
    public function isEmpty(): bool
    {
        return empty($this->routes);
    }

    /**
     * Returns the path to the cache file for invalidation checks.
     */
    public function getCacheFile(): string
    {
        return $this->cacheFile;
    }

    /**
     * Loads routes from the compiled cache file.
     */
    private function loadRoutes(): void
    {
        if (file_exists($this->cacheFile)) {
            $this->routes = require $this->cacheFile;
        }
    }
}