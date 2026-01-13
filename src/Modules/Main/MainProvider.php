<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */
declare(strict_types=1);

namespace Modestox\Modules\Main;

use Modestox\Core\ModuleInterface;
use Modestox\Core\Router;
use Modestox\Modules\Main\Controller\HomeController;

/**
 * Main module configuration provider
 */
class MainProvider implements ModuleInterface
{
    public function boot(): void
    {
        // Registering routes for this specific module
        Router::add('/', HomeController::class, 'index');
        Router::add('/home', HomeController::class, 'index');
    }
}