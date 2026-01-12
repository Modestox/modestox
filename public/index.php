<?php
/**
 * Modestox CMS - Entry Point
 *
 * @package    Modestox
 * @license    GNU AGPL v3
 * @link       https://github.com/Modestox/modestox
 */

declare(strict_types=1);

// Security: Define a constant to check in other files
define('MODESTOX_ACCESS', true);

// Record start time for performance monitoring
define('MODESTOX_START_TIME', microtime(true));

echo "<h1>🛡️ Modestox CMS</h1>";
echo "<p>Status: <strong>Alpha Development</strong></p>";
echo "<hr>";
echo "System core is booting...";
