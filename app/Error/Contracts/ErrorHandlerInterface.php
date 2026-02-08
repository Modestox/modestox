<?php
/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Error\Contracts;

use Throwable;

/**
 * Interface ErrorHandlerInterface
 * Defines the contract for handling system-wide exceptions and errors.
 */
interface ErrorHandlerInterface
{
    /**
     * Handle the exception, set headers and render error output.
     */
    public function handle(Throwable $exception): void;
}