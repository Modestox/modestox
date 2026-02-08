<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Bootstrap;

/**
 * Class Config
 * Manages application settings and environment state.
 */
class Config
{
    private array $data = [];
    private string $configPath;

    public function __construct()
    {
        // Путь должен вести в app/etc/config.php
        // __DIR__ это app/Bootstrap, идем на один уровень вверх к app/
        $this->configPath = dirname(__DIR__) . '/etc/config.php';
        $this->load();
    }

    public function isDev(): bool
    {
        return ($this->data['env'] ?? 'production') === 'development';
    }

    public function getEnv(): string
    {
        return $this->data['env'] ?? 'production';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $value = $this->data;

        foreach ($parts as $part) {
            if (!isset($value[$part])) return $default;
            $value = $value[$part];
        }

        return $value;
    }

    private function load(): void
    {
        if (file_exists($this->configPath)) {
            $this->data = require $this->configPath;
        }
    }
}