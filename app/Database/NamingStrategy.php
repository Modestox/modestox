<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Database;

/**
 * Class NamingStrategy
 * Handles conversion between database snake_case and DTO camelCase.
 */
class NamingStrategy
{
    /**
     * Converts a snake_case string to camelCase.
     * Example: "is_active" -> "isActive"
     */
    public function toCamelCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    /**
     * Converts a camelCase string to snake_case.
     * Example: "createdAt" -> "created_at"
     */
    public function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', $string));
    }
}