<?php
/**
 * Modestox CMS - Entry Point
 */

declare(strict_types=1);

// Security: Check access in other files
define('MODESTOX_ACCESS', true);

// 1. Load the Core file manually (until Composer autoloader is ready)
require_once __DIR__ . '/../src/Modestox/Core.php';

// 2. Initialize the Core
$app = new \Modestox\Core();

// 3. Output
echo "<h1>🛡️ Modestox CMS</h1>";
echo "<p>Status: <strong>Active Development</strong></p>";
echo "<hr>";

// Run the core engine
$app->run();
