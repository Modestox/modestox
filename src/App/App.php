<?php

declare(strict_types=1);

namespace Core\App;

use Core\Routing\Router;
use Core\Compiler\RouteCompiler;
use Core\Routing\Contracts\ControllerInterface;

class App
{
    public function run(): void
    {
        (new RouteCompiler())->compile();

        $router = new Router();
        $controllerClass = $router->resolve($_SERVER['REQUEST_URI']);

        if ($controllerClass && class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if ($controller instanceof ControllerInterface) {
                $controller->execute();
                return;
            }
        }

        $this->abort404();
    }

    private function abort404(): void
    {
        header("HTTP/1.1 404 Not Found");
        echo "Modestox Core: Route not found.";
    }
}