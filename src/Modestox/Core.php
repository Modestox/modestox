<?php
/**
 * Modestox CMS - Core Engine
 */

declare(strict_types=1);

namespace Modestox;

// Security check
if (!defined('MODESTOX_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access denied.');
}

class Core
{
    public const VERSION = '0.0.1-alpha';

    public function __construct()
    {
        // Initialization logic will go here
    }

    public function run(): void
    {
        echo "<br>Modestox Core v" . self::VERSION . " is now active.";
    }
}
