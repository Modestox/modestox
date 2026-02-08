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
 * Class RouteCompiler
 * Scans module directories and generates a map of URLs to Controller classes.
 */
class RouteCompiler extends BaseCompiler
{
    private readonly string $cacheFile;

    public function __construct()
    {
        parent::__construct();
        // Используем путь к кэшу из родительского класса
        $this->cacheFile = $this->cachePath . '/routes.php';
    }

    public function compile(): void
    {
        $routes = [];

        if (!is_dir($this->modulesPath)) {
            $this->saveCache($this->cacheFile, $routes);
            return;
        }

        // Сканируем папку modules/{Vendor}
        $vendors = array_diff(scandir($this->modulesPath), ['.', '..']);

        foreach ($vendors as $vendor) {
            $vendorPath = $this->modulesPath . '/' . $vendor;
            if (!is_dir($vendorPath)) {
                continue;
            }

            // Сканируем папку modules/{Vendor}/{Module}
            $modules = array_diff(scandir($vendorPath), ['.', '..']);

            foreach ($modules as $module) {
                $ctrlPath = "$vendorPath/$module/Controller";
                if (is_dir($ctrlPath)) {
                    $this->collect($routes, $vendor, $module, $ctrlPath);
                }
            }
        }

        $this->saveCache($this->cacheFile, $routes);
    }

    /**
     * Maps controller files to routes and FQCN (Fully Qualified Class Names).
     */
    private function collect(array &$routes, string $v, string $m, string $path): void
    {
        $files = array_diff(scandir($path), ['.', '..']);

        foreach ($files as $file) {
            if (str_ends_with($file, '.php')) {
                $name = str_replace('.php', '', $file);

                // Формируем URL: каталог/индекс -> каталог, каталог/листинг -> каталог/листинг
                $url = strtolower($m) . ($name === 'Index' ? '' : '/' . strtolower($name));

                // ВНИМАНИЕ: Добавляем префикс Modules согласно PSR-4 в composer.json
                $routes[$url] = "Modules\\$v\\$m\\Controller\\$name";
            }
        }
    }
}