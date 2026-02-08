<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Container;

use Core\Container\Contracts\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use RuntimeException;
use Throwable;
use Closure;

/**
 * Class Container
 * Smart Dependency Injection Container with Autowiring support.
 */
class Container implements ContainerInterface
{
    /** @var array<string, mixed> Cached service instances */
    private array $instances = [];

    /** @var array<string, mixed> Service definitions */
    private array $definitions = [];

    /**
     * Retrieve a service from the container.
     */
    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        return $this->resolve($id);
    }

    /**
     * Check if the service exists in the container or can be resolved.
     */
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || isset($this->instances[$id]) || class_exists($id);
    }

    /**
     * Register a service definition.
     */
    public function set(string $id, mixed $concrete): void
    {
        $this->definitions[$id] = $concrete;
    }

    /**
     * Resolve the class dependencies using Reflection API.
     */
    private function resolve(string $id): mixed
    {
        $concrete = $this->definitions[$id] ?? $id;

        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (Throwable $e) {
            throw new RuntimeException("Target class [$concrete] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return $this->instances[$id] = new $concrete();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        return $this->instances[$id] = $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Build the dependency list for a constructor.
     */
    private function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new RuntimeException("Unresolvable dependency [{$parameter->getName()}] in class constructor.");
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $dependencies;
    }
}