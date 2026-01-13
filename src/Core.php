<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */
declare(strict_types=1);

namespace Modestox;

if (!defined('MODESTOX_ACCESS')) {
    die('Direct access denied.');
}

class Core
{
    public function run(): void
    {
        $router = new Router();
        $router->resolve();
    }
}