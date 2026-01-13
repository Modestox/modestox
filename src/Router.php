<?php

/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */
declare(strict_types=1);

namespace Modestox;

use Modestox\Controller\HomeController;

class Router
{
    private string $uri;

    public function __construct()
    {
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    }

    public function resolve(): void
    {
        $path = trim($this->uri, '/');

        match ($path) {
            ''      => (new HomeController())->index(),
            'admin' => $this->renderAdminStub(),
            default => $this->notFound()
        };
    }

    private function renderAdminStub(): void
    {
        echo "<h2>Admin Panel coming soon...</h2>";
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo "<h2>404 - Page Not Found</h2>";
    }
}