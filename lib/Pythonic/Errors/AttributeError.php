<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class AttributeError extends Error
{

    public static function ofReadOnlyAttribute(string|object $class, string $attribute): static
    {
        if ( ! is_string($class))
        {
            $class = get_class($class);
        }

        return static::message("'%s' object attribute '%s' is read-only", static::classname($class), $attribute);
    }

    public static function raiseForReadOnlyAttribute(string|object $class, string $attribute): never
    {

        throw static::ofReadOnlyAttribute($class, $attribute);
    }

    public static function ofClassAttribute(string|object $class, string $attribute): static
    {
        if ( ! is_string($class))
        {
            $class = get_class($class);
        }

        return static::message("'%s' object has no attribute '%s'", static::classname($class), $attribute);
    }

    public static function raiseForClassAttribute(string|object $class, string $attribute): never
    {

        throw static::ofClassAttribute($class, $attribute);
    }

    public static function of(string $attribute): static
    {
        return static::message('%s: %s', static::classname(), $attribute);
    }

    public static function raiseForAttribute(string $attribute)
    {

        throw static::of($attribute);
    }

}
