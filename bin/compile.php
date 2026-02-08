<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Shared\Compiler\RouteCompiler;
use Core\Shared\Compiler\LanguageCompiler;

/**
 * Global compilation script for system performance optimization.
 */
try {
    echo "Starting compilation...\n";

    // 1. Compile Routes
    (new RouteCompiler())->compile();
    echo "- Routes compiled.\n";

    // 2. Compile Languages from CSV
    (new LanguageCompiler())->compile();
    echo "- Languages compiled.\n";

    echo "Done! System is ready.\n";
} catch (Throwable $e) {
    echo "Error during compilation: " . $e->getMessage() . "\n";
    exit(1);
}