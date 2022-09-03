<?php

declare(strict_types=1);

namespace Pythonic\Utils;

abstract class Utils
{

    /**
     * Checks recursively if a class uses a trait
     */
    public static function uses_trait(string|object $class, string $trait): bool
    {
        return in_array($trait, static::class_uses_recursive($class));
    }

    /**
     * Returns all traits used by a trait and its traits.
     */
    public static function trait_uses_recursive(string $trait): array
    {
        $traits = class_uses($trait) ?: [];

        foreach ($traits as $trait)
        {
            $traits += static:: trait_uses_recursive($trait);
        }

        return $traits;
    }

    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     */
    public static function class_uses_recursive(object|string $class): array
    {
        if (is_object($class))
        {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class)
        {
            $results += static::trait_uses_recursive($class);
        }

        return array_unique($results);
    }

}
