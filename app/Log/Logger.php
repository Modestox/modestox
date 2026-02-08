<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Log;

use Core\Bootstrap\Config;
use RuntimeException;
use Throwable;

/**
 * Class Logger
 * Handles environment-aware logging with separate channels for errors and exceptions.
 */
class Logger
{
    private string $logBaseDir;

    public function __construct(
        private readonly Config $config
    ) {
        // Path to var/log
        $this->logBaseDir = dirname(__DIR__, 2) . '/var/log';
    }

    /**
     * Log a detailed exception with stack trace.
     */
    public function logException(Throwable $e): void
    {
        $message = sprintf(
            "[%s] %s: %s in %s on line %d\nStack trace:\n%s\n" . str_repeat('-', 50) . "\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        $this->write('exceptions.log', $message);
    }

    /**
     * Log general errors, warnings or notices.
     */
    public function logError(string $message, string $level = 'ERROR'): void
    {
        $formatted = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message);
        $this->write('errors.log', $formatted);
    }

    /**
     * Ensures the environment-specific directory exists and writes the message.
     */
    private function write(string $filename, string $message): void
    {
        // Example path: var/log/development/exceptions.log
        $targetDir = $this->logBaseDir . '/' . $this->config->getEnv();

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
            }
        }

        file_put_contents($targetDir . '/' . $filename, $message, FILE_APPEND);
    }
}