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

/**
 * Class LanguageCompiler
 * Aggregates all module CSV translations into a high-performance PHP cache.
 */
class LanguageCompiler extends BaseCompiler
{
    private readonly string $cacheFile;

    public function __construct()
    {
        parent::__construct();
        $this->cacheFile = $this->cachePath . '/languages.php';
    }

    /**
     * Scans all modules and compiles their CSV files.
     */
    public function compile(): void
    {
        $languages = [];

        if (!is_dir($this->modulesPath)) {
            $this->saveCache($this->cacheFile, $languages);
            return;
        }

        // Scan Vendors
        foreach (array_diff(scandir($this->modulesPath), ['.', '..']) as $vendor) {
            $vendorPath = $this->modulesPath . '/' . $vendor;
            if (!is_dir($vendorPath)) {
                continue;
            }

            // Scan Modules
            foreach (array_diff(scandir($vendorPath), ['.', '..']) as $module) {
                $i18nPath = "$vendorPath/$module/i18n";
                if (is_dir($i18nPath)) {
                    $this->collectFromCsv($languages, $i18nPath);
                }
            }
        }

        $this->saveCache($this->cacheFile, $languages);
    }

    /**
     * Parses individual CSV files and merges them into the result.
     */
    private function collectFromCsv(array &$languages, string $path): void
    {
        foreach (array_diff(scandir($path), ['.', '..']) as $file) {
            if (str_ends_with($file, '.csv')) {
                $locale = str_replace('.csv', '', $file);

                if (($handle = fopen("$path/$file", "r")) !== false) {
                    while (($data = fgetcsv($handle, 0, ",")) !== false) {
                        if (isset($data[0], $data[1])) {
                            // Trim to avoid whitespace issues from CSV editors
                            $languages[$locale][trim($data[0])] = trim($data[1]);
                        }
                    }
                    fclose($handle);
                }
            }
        }
    }
}