<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

use Core\Bootstrap\Application;
use Core\Language\Contracts\TranslatorInterface;

if (!function_exists('dd')) {
    /**
     * Dump and Die.
     */
    function dd(mixed ...$vars): void
    {
        foreach ($vars as $v) {
            echo '<pre style="background:#111; color:#0f0; padding:15px; border-radius:5px; border: 1px solid #333; overflow: auto;">';
            var_dump($v);
            echo '</pre>';
        }
        die();
    }
}

if (!function_exists('__')) {
    /**
     * Global translation helper.
     */
    function __(string $text, array $args = [], ?string $locale = null): string
    {
        static $translator = null;

        if ($translator === null) {
            // Берем транслятор из контейнера один раз за запрос
            $translator = Application::getContainer()->get(TranslatorInterface::class);
        }

        return $translator->translate($text, $args, $locale);
    }
}