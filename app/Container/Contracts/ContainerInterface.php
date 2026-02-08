<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Container\Contracts;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Interface ContainerInterface
 * Standard PSR-11 interface for our Dependency Injection Container.
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Register a shared service (singleton).
     */
    public function set(string $id, mixed $concrete): void;
}