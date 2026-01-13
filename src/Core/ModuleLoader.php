<?php
declare(strict_types=1);

namespace Modestox\Core;

class ModuleLoader
{
    private array $modules = [];
    private array $middleware = []; // Store registered middleware

    public function __construct(private string $modulesPath) {}

    public function bootstrap(): void
    {
        if (!is_dir($this->modulesPath)) return;

        $folders = array_diff(scandir($this->modulesPath), ['.', '..']);

        foreach ($folders as $folder) {
            $providerClass = "Modestox\\Modules\\{$folder}\\{$folder}Provider";

            if (class_exists($providerClass)) {
                $module = new $providerClass();
                if ($module instanceof ModuleInterface) {
                    $module->boot();

                    // Check if module provides any middleware
                    if (method_exists($module, 'getMiddleware')) {
                        $this->middleware = array_merge($this->middleware, $module->getMiddleware());
                    }

                    $this->modules[$folder] = $module;
                }
            }
        }
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }
}