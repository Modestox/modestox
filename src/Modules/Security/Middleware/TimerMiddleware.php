<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */
declare(strict_types=1);

namespace Modestox\Modules\Security\Middleware;

use Modestox\Core\MiddlewareInterface;

/**
 * Simple middleware to measure execution time
 */
class TimerMiddleware implements MiddlewareInterface
{
    public function handle(mixed $request, callable $next): mixed
    {
        $start = microtime(true);

        // Pass the request further down the chain
        $response = $next($request);

        $duration = microtime(true) - $start;

        // Optionally append info to output (for debug)
        echo "<div style='background: #000; color: #0f0; padding: 10px; position: fixed; bottom: 0; right: 0;'>";
        echo "Execution time: " . round($duration, 4) . "s";
        echo "</div>";

        return $response;
    }
}