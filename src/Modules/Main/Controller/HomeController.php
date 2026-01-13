<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */
declare(strict_types=1);

namespace Modestox\Modules\Main;

namespace Modestox\Modules\Main\Controller;

use Modestox\Controller\BaseController;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render("<h1>Welcome to Modestox</h1><p>Hybrid Modular Engine is running.</p>");
    }
}