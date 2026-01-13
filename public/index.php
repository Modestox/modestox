<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */
declare(strict_types=1);

/**
 * Modestox CMS - Bootloader
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Modestox\Core\ModuleLoader;
use Modestox\Core\Router;
use Modestox\Core\Pipeline;

// 1. Boot modules
$loader = new ModuleLoader(__DIR__ . '/../src/Modules');
$loader->bootstrap();

// 2. Run Global Middleware Pipeline
// We send the current URI through the chain
(new Pipeline())
    ->through($loader->getMiddleware())
    ->send($_SERVER['REQUEST_URI'])
    ->then(function($uri) {
        // 3. This is the final destination after all middleware
        $router = new Router();
        $router->resolve();
    });