<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Modestox\Modules\Security;

use Modestox\Core\ModuleInterface;
use Modestox\Modules\Security\Middleware\TimerMiddleware;

class SecurityProvider implements ModuleInterface
{
    public function boot(): void
    {
        // Settings for security module if needed
    }

    /**
     * This method is called by ModuleLoader to collect all middlewares
     */
    public function getMiddleware(): array
    {
        return [
            TimerMiddleware::class
        ];
    }
}