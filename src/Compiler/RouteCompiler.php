<?php

declare(strict_types=1);

namespace Core\Compiler;

class RouteCompiler
{
    private string $modulesPath;
    private string $cacheFile;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $this->modulesPath = $root . '/app/modules';
        $this->cacheFile = $root . '/var/cache/routes.php';
    }

    public function compile(): void
    {
        $routes = [];
        if (!is_dir($this->modulesPath)) {
            $this->saveCache($routes);
            return;
        }

        foreach (array_diff(scandir($this->modulesPath), ['.', '..']) as $vendor) {
            $vendorPath = $this->modulesPath . '/' . $vendor;
            if (!is_dir($vendorPath)) {
                continue;
            }

            foreach (array_diff(scandir($vendorPath), ['.', '..']) as $module) {
                $ctrlPath = "$vendorPath/$module/Controller";
                if (is_dir($ctrlPath)) {
                    $this->collect($routes, $vendor, $module, $ctrlPath);
                }
            }
        }
        $this->saveCache($routes);
    }

    private function collect(array &$routes, string $v, string $m, string $path): void
    {
        foreach (array_diff(scandir($path), ['.', '..']) as $file) {
            if (str_ends_with($file, '.php')) {
                $name = str_replace('.php', '', $file);
                $url = strtolower($m) . ($name === 'Index' ? '' : '/' . strtolower($name));
                $routes[$url] = "$v\\$m\\Controller\\$name";
            }
        }
    }

    private function saveCache(array $routes): void
    {
        $directory = dirname($this->cacheFile);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        $export = var_export($routes, true);

        $export = preg_replace('/array\s*\(/', '[', $export);
        $export = preg_replace('/\)\s*$/m', ']', $export);
        $export = str_replace('),', '],', $export);

        $code = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . $export . ";\n";
        file_put_contents($this->cacheFile, $code);
    }
}