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

/**
 * Class QueryBuilder
 * Lightweight SQL builder with automatic DTO hydration and schema filtering.
 */
class QueryBuilder
{
    private array $where = [];
    private array $params = [];

    public function __construct(
        private readonly PDO $pdo,
        private readonly Hydrator $hydrator,
        private readonly Extractor $extractor,
        private readonly string $table,
    ) {}

    /**
     * Adds a WHERE condition to the query.
     */
    public function where(string $column, mixed $value, string $operator = '='): self
    {
        $this->where[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    /**
     * Executes the query and automatically hydrates the first result into a DTO.
     *
     * @template T
     * @param class-string<T> $dtoClass
     * @return T|null
     */
    public function fetch(string $dtoClass): ?object
    {
        $stmt = $this->executeSelect();
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return $this->hydrator->hydrate($dtoClass, $result);
    }

    /**
     * Fetches multiple records as an array of DTOs.
     *
     * @template T
     * @param class-string<T> $dtoClass
     * @return array<T>
     */
    public function fetchAll(string $dtoClass): array
    {
        $stmt = $this->executeSelect();
        $results = $stmt->fetchAll();

        return array_map(
            fn(array $row) => $this->hydrator->hydrate($dtoClass, $row),
            $results
        );
    }

    /**
     * Inserts data into the database using a DTO or raw array with schema filtering.
     */
    public function insert(object|array $data): bool
    {
        if (is_object($data)) {
            $data = $this->extractor->extractForTable($data, $this->table, $this->pdo);
        }

        if (empty($data)) {
            return false;
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        return $this->pdo->prepare($sql)->execute(array_values($data));
    }

    /**
     * Updates records based on DTO data and where conditions with schema filtering.
     */
    public function update(object|array $data): bool
    {
        if (is_object($data)) {
            $data = $this->extractor->extractForTable($data, $this->table, $this->pdo);
        }

        if (empty($data)) {
            return false;
        }

        $sets = array_map(fn($col) => "{$col} = ?", array_keys($data));

        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE %s",
            $this->table,
            implode(', ', $sets),
            implode(' AND ', $this->where)
        );

        // Merge SET values with WHERE parameters
        $params = array_merge(array_values($data), $this->params);

        return $this->pdo->prepare($sql)->execute($params);
    }

    /**
     * Prepares and executes the SELECT statement.
     */
    private function executeSelect(): \PDOStatement
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt;
    }
}