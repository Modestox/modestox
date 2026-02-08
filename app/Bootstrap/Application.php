<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Bootstrap;

use Core\Container\Container;
use Core\Container\Contracts\ContainerInterface;
use Core\Database\Connection;
use Core\Database\Extractor;
use Core\Database\Hydrator;
use Core\Database\NamingStrategy;
use Core\Error\Contracts\ErrorHandlerInterface;
use Core\Error\ErrorHandler;
use Core\Http\Exceptions\HttpException;
use Core\Http\Router;
use Core\Http\Routing\Contracts\ControllerInterface;
use Core\Language\Contracts\TranslatorInterface;
use Core\Language\Translator;
use Core\Log\Logger;
use ErrorException;
use Throwable;

/**
 * Class Application
 * The central kernel of Modestox CMS.
 * Orchestrates DI Container, Error Handling, and Request Lifecycle.
 */
class Application
{
    /** @var ContainerInterface Static instance for global helpers like __() */
    private static ContainerInterface $containerInstance;

    private readonly ContainerInterface $container;
    private readonly ErrorHandlerInterface $errorHandler;

    public function __construct()
    {
        // 1. Initialize DI Container
        $this->container = new Container();
        self::$containerInstance = $this->container;

        // 2. Register all core services
        $this->bootContainer();

        // 3. Initialize Error Handling with Logger and Config
        $this->errorHandler = $this->container->get(ErrorHandlerInterface::class);
        $this->initializeErrorHandling();
    }

    /**
     * Entry point for the web request.
     */
    public function run(): void
    {
        try {
            $this->syncCache(); // Авто-обновление кэша

            /** @var Router $router */
            $router = $this->container->get(Router::class);

            if ($router->isEmpty()) {
                throw new HttpException("Modestox Kernel is running. No modules detected.", 503);
            }

            $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            $controllerClass = $router->resolve($uri);

            if (!$controllerClass || !class_exists($controllerClass)) {
                throw HttpException::notFound("Route not found: " . htmlspecialchars($uri));
            }

            // Resolve controller via Container (Automatic Dependency Injection)
            $controller = $this->container->get($controllerClass);

            if (!$controller instanceof ControllerInterface) {
                throw new HttpException("Controller must implement ControllerInterface.", 500);
            }

            $controller->execute();
        } catch (Throwable $e) {
            $this->errorHandler->handle($e);
        }
    }

    /**
     * Map interfaces to concrete classes and register core services.
     */
    private function bootContainer(): void
    {
        // Infrastructure
        $this->container->set(Config::class, Config::class);
        $this->container->set(Logger::class, Logger::class);

        // Core Services
        $this->container->set(ErrorHandlerInterface::class, ErrorHandler::class);
        $this->container->set(TranslatorInterface::class, Translator::class);
        $this->container->set(Router::class, Router::class);

        // Database Layer
        $this->container->set(NamingStrategy::class, NamingStrategy::class);
        $this->container->set(Extractor::class, Extractor::class);
        $this->container->set(Hydrator::class, Hydrator::class);
        $this->container->set(Connection::class, Connection::class);
        $this->container->set(NamingStrategy::class, NamingStrategy::class);
        $this->container->set(Hydrator::class, Hydrator::class);

        // Register the container itself
        $this->container->set(ContainerInterface::class, $this->container);
    }

    /**
     * Provides global access to the DI Container.
     */
    public static function getContainer(): ContainerInterface
    {
        return self::$containerInstance;
    }

    /**
     * Synchronizes cache files via DI Container in development mode.
     */
    private function syncCache(): void
    {
        /** @var Config $config */
        $config = $this->container->get(Config::class);

        if (!$config->isDev()) {
            return;
        }

        $routeCompiler = $this->container->get(\Core\Shared\Compiler\RouteCompiler::class);
        $langCompiler = $this->container->get(\Core\Shared\Compiler\LanguageCompiler::class);

        // Указываем пути напрямую, не трогая Router раньше времени
        $routeCache = dirname(__DIR__, 2) . '/var/cache/routes.php';
        $langCache = dirname(__DIR__, 2) . '/var/cache/languages.php';

        if ($routeCompiler->isStale($routeCache)) {
            $routeCompiler->compile();
        }

        if ($langCompiler->isStale($langCache)) {
            $langCompiler->compile();
        }
    }

    /**
     * Converts standard PHP errors into ErrorExceptions for uniform handling.
     */
    private function initializeErrorHandling(): void
    {
        set_error_handler(function (int $severity, string $message, string $file, int $line): void {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
    }
}