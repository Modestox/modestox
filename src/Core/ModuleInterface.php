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
 * Interface ModuleInterface
 * Contract for all modules to interact with the Core.
 */
interface ModuleInterface
{
    /**
     * Bootstraps the module (registers routes, listeners, etc.)
     */
    public function boot(): void;
}