<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

namespace Modestox\Core;

/**
 * Interface MiddlewareInterface
 * All middleware links must implement this handle method.
 */
interface MiddlewareInterface
{
    /**
     * @param mixed $request Data being passed through the pipe
     * @param callable $next The next link in the chain
     * @return mixed
     */
    public function handle(mixed $request, callable $next): mixed;
}