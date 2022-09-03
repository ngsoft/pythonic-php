<?php

declare(strict_types=1);

namespace Pythonic\Traits;

trait ClassUtils
{

    /**
     * Alias to static::class
     */
    protected static function class(): string
    {
        return static::class;
    }

    /**
     * Get class name without the namespace
     */
    protected static function classname(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    /**
     * Get the namespace of the class
     */
    protected static function namespace(): string
    {

        $class = static::class;

        if ( ! str_contains($class, NAMESPACE_SEPARATOR))
        {
            return '';
        }
        return substr($class, 0, strrpos($class, NAMESPACE_SEPARATOR));
    }

    /**
     * Checks if class extends or is static
     */
    protected static function isSelf(object|string $class)
    {
        return is_a($class, static::class, is_string($class));
    }

}
