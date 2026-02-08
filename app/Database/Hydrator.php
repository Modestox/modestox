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

use ReflectionClass;
use ReflectionProperty;

/**
 * Class Hydrator
 * Populates DTO objects with data from database arrays.
 */
class Hydrator
{
    public function __construct(
        private readonly NamingStrategy $namingStrategy
    ) {}

    /**
     * Hydrates a single object of the given class with data.
     *
     * @template T
     * @param class-string<T> $className
     * @param array $data
     * @return T
     */
    public function hydrate(string $className, array $data): object
    {
        $reflection = new ReflectionClass($className);
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($data as $column => $value) {
            // Convert snake_case column to camelCase property
            $propertyName = $this->namingStrategy->toCamelCase($column);

            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                $this->setPropertyValue($property, $instance, $value);
            }
        }

        return $instance;
    }

    /**
     * Sets the value of a property, handling types if necessary.
     */
    private function setPropertyValue(ReflectionProperty $property, object $instance, mixed $value): void
    {
        // Skip if value is null and property doesn't allow it
        if ($value === null && !$property->getType()?->allowsNull()) {
            return;
        }

        // Basic type casting based on property type hint
        $type = $property->getType()?->getName();
        if ($type === 'int') $value = (int)$value;
        if ($type === 'float') $value = (float)$value;
        if ($type === 'bool') $value = (bool)$value;

        $property->setValue($instance, $value);
    }
}