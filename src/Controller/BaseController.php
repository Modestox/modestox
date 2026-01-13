<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Modestox\Controller;

/**
 * Class BaseController
 * Shared logic for all module controllers.
 */
abstract class BaseController
{
    protected string $lang = 'en';

    protected function render(string $content): void
    {
        echo "<!DOCTYPE html><html lang='{$this->lang}'><head><meta charset='UTF-8'>";
        echo "<title>Modestox CMS</title></head><body>";
        echo "<header><nav><a href='/'>Home</a></nav></header><hr>";
        echo $content;
        echo "</body></html>";
    }
}