<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class NotImplementedError extends RuntimeError
{

    protected function __default__(): string
    {
        return 'Not implemented';
    }

    public static function raiseForMethod(object|string $class, string $method): never
    {
        if (is_object($class))
        {
            $class = get_class($class);
        }
        throw static::message('Method %s must be implemented by %s', $message, $class);
    }

}
