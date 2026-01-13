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

abstract class BaseController
{
    protected string $lang = 'en';

    protected function render(string $content): void
    {
        // Базовый каркас страницы
        echo "<!DOCTYPE html><html lang='{$this->lang}'><head><title>Modestox CMS</title></head><body>";
        echo "<header><nav><a href='/'>Home</a> | <a href='/admin'>Admin</a></nav></header><hr>";
        echo $content;
        echo "</body></html>";
    }
}