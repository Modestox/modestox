<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Shared\Compiler;

use RuntimeException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class BaseCompiler
 * Abstract base for all system compilers with smart invalidation logic.
 */
abstract class BaseCompiler
{
    protected readonly string $rootPath;
    protected readonly string $modulesPath;
    protected readonly string $cachePath;

    public function __construct()
    {
        $this->rootPath = dirname(__DIR__, 3);
        $this->modulesPath = $this->rootPath . '/modules';
        $this->cachePath = $this->rootPath . '/var/cache';
    }

    /**
     * Checks if the cache file is older than any file in the modules directory.
     */
    public function isStale(string $cacheFile): bool
    {
        if (!file_exists($cacheFile)) {
            return true;
        }

        $cacheTime = filemtime($cacheFile);

        // Рекурсивно проверяем файлы в модулях
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->modulesPath)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() > $cacheTime) {
                return true; // Найден файл, который новее кэша
            }
        }

        return false;
    }

    /**
     * Formats and saves data as a highly-optimized PHP array in the cache.
     */
    protected function saveCache(string $filePath, array $data): void
    {
        $directory = dirname($filePath);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        $export = var_export($data, true);
        $export = preg_replace('/array\s*\(/', '[', $export);
        $export = preg_replace('/\)\s*$/m', ']', $export);
        $export = str_replace('),', '],', $export);

        $code = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . $export . ";\n";
        file_put_contents($filePath, $code);
    }
}