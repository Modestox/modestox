<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Language;

use Core\Language\Contracts\TranslatorInterface;

/**
 * Class Translator
 * Handles string translations using cached data from module CSV files.
 */
class Translator implements TranslatorInterface
{
    /** @var array<string, array<string, string>> Cached dictionary data */
    private array $dictionary = [];

    /**
     * Translator constructor.
     * Loads the compiled language map from the cache directory.
     */
    public function __construct(
        private readonly string $defaultLocale = 'en_US',
    ) {
        // Calculate path to the compiled cache file
        $cacheFile = dirname(__DIR__, 2) . '/var/cache/languages.php';

        if (file_exists($cacheFile)) {
            $this->dictionary = require $cacheFile;
        }
    }

    /**
     * Translates the given text based on the locale and provides variable substitution.
     */
    public function translate(string $text, array $args = [], ?string $locale = null): string
    {
        $locale = $locale ?? $this->defaultLocale;
        $translation = $this->dictionary[$locale][$text] ?? $text;

        if (empty($args)) {
            return $translation;
        }

        /**
         * Используем vsprintf, что позволяет в CSV писать:
         * "Hello %s" (простой M1)
         * ИЛИ
         * "Price: %2$s %1$d" (индексированный M2 style)
         */
        return vsprintf($translation, $args);
    }
}