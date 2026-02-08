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
use Core\Bootstrap\Config;

/**
 * Class Connection
 * Manages PDO connection and provides QueryBuilder instances.
 */
class Connection
{
    private ?PDO $pdo = null;

    public function __construct(
        private readonly Config $config,
        private readonly Hydrator $hydrator
    ) {}

    /**
     * Returns a new QueryBuilder instance.
     */
    public function select(string $table): QueryBuilder
    {
        return new QueryBuilder($this->getPdo(), $this->hydrator, $table);
    }

    private function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $db = $this->config->get('db');
            $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8mb4";

            $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return $this->pdo;
    }
}