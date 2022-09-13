<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class NotImplementedError extends RuntimeError
{

    protected function __default__(): string
    {
        return 'Not implemented';
    }

    public static function forMethod(object|string $class, string $method): static
    {
        if (is_object($class))
        {
            $class = get_class($class);
        }
        return static::message('Method %s must be implemented by %s', $method, $class)
    }

    public static function raiseForMethod(object|string $class, string $method): never
    {

        throw static::forMethod($class, $method);
    }

}
