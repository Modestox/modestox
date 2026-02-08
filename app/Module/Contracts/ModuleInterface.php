<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Module\Contracts;

/**
 * Interface ModuleInterface
 * Defines the contract for all system modules.
 */
interface ModuleInterface
{
    /**
     * Register module services in the DI container.
     */
    // public function register(ContainerInterface $container): void;

    /**
     * Boot the module (subscribe to events, routes, etc.)
     */
    public function boot(): void;
}