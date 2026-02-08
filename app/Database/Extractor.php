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

use PDO;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class Extractor
 * Converts DTOs to database-ready arrays with automatic schema filtering.
 */
class Extractor
{
    /** @var array<string, array<string>> Simple in-memory cache for table columns */
    private array $schemaCache = [];

    public function __construct(
        private readonly NamingStrategy $namingStrategy
    ) {}

    /**
     * Extracts and filters DTO properties based on the actual database table schema.
     */
    public function extractForTable(object $dto, string $table, PDO $pdo): array
    {
        $allProperties = $this->extractAll($dto);
        $tableColumns = $this->getTableColumns($table, $pdo);

        // Фильтруем: оставляем только те ключи, которые есть в колонках таблицы
        return array_intersect_key($allProperties, array_flip($tableColumns));
    }

    /**
     * Extracts all initialized properties from DTO and converts to snake_case.
     */
    private function extractAll(object $dto): array
    {
        $data = [];
        $reflection = new ReflectionClass($dto);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            if (!$property->isInitialized($dto)) {
                continue;
            }

            $columnName = $this->namingStrategy->toSnakeCase($property->getName());
            $data[$columnName] = $property->getValue($dto);
        }

        return $data;
    }

    /**
     * Fetches column names for a specific table from the database.
     * Uses in-memory caching to avoid redundant queries during a single request.
     */
    private function getTableColumns(string $table, PDO $pdo): array
    {
        if (isset($this->schemaCache[$table])) {
            return $this->schemaCache[$table];
        }

        $stmt = $pdo->query("DESCRIBE `{$table}`");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $this->schemaCache[$table] = $columns;
    }
}