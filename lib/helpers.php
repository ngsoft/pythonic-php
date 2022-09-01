<?php

declare(strict_types=1);

/**
 * From ngsoft/tools ^3
 */
if ( ! defined('NAMESPACE_SEPARATOR'))
{
    define('NAMESPACE_SEPARATOR', '\\');
}

if ( ! defined('PHP_EXT'))
{
    define('PHP_EXT', '.php');
}

if ( ! defined('SCRIPT_START'))
{
    define('SCRIPT_START', $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));
}

if ( ! defined('DATE_DB'))
{
    define('DATE_DB', 'Y-m-d H:i:s');
}

if ( ! function_exists('class_namespace'))
{

    /**
     * Get the namespace from a class
     *
     * @param string|object $class
     * @return string
     */
    function class_namespace(string|object $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        if ( ! str_contains($class, NAMESPACE_SEPARATOR))
        {
            return '';
        }
        return substr($class, 0, strrpos($class, NAMESPACE_SEPARATOR));
    }

}


if ( ! function_exists('is_stringable'))
{

    function is_stringable(mixed $value): bool
    {
        if (is_scalar($value) || is_null($value))
        {
            return true;
        }
        if ($value instanceof Stringable)
        {
            return true;
        }

        if (is_object($value) && method_exists($value, '__toString'))
        {
            return true;
        }

        return false;
    }

}


if ( ! function_exists('str_val'))
{

    /**
     * Get string value of a variable
     */
    function str_val(mixed $value): string
    {
        if (is_string($value))
        {
            return $value;
        }

        if (is_null($value))
        {
            return '';
        }

        if (is_bool($value))
        {
            return $value ? 'true' : 'false';
        }

        if (is_numeric($value))
        {
            return (string) $value;
        }


        if ( ! is_stringable($value))
        {
            throw new InvalidArgumentException(sprintf('Text of type %s is not stringable.', get_debug_type($value)));
        }


        return (string) $value;
    }

}



if ( ! function_exists('is_arrayaccess'))
{

    /**
     * Check if value is Array like
     */
    function is_arrayaccess(mixed $value): bool
    {

        if (is_array($value))
        {
            return true;
        }


        return $value instanceof ArrayAccess && $value instanceof Countable;
    }

}

if ( ! function_exists('is_unsigned'))
{

    /**
     * Checks if value is positive
     */
    function is_unsigned(mixed $value): bool
    {
        $value = str_val($value);
        return is_numeric($value) && (int) $value >= 0;
    }

}

if ( ! function_exists('uses_trait'))
{

    /**
     * Checks recursively if a class uses a trait
     *
     * @param string|object $class
     * @param string $trait
     * @return bool
     */
    function uses_trait(string|object $class, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($class));
    }

}


if ( ! function_exists('is_instanciable'))
{

    function is_instanciable(string $class): bool
    {
        return class_exists($class) && (new ReflectionClass($class))->isInstantiable();
    }

}

if ( ! function_exists('in_range'))
{

    /**
     * Checks if number is in range
     */
    function in_range(int $number, int $min, int $max, bool $inclusive = true)
    {


        if ($min === $max)
        {
            return $number === $min;
        }

        if ($min > $max)
        {
            [$min, $max] = [$max, $min];
        }

        if ($inclusive)
        {

            return $number >= $min && $number <= $max;
        }


        return $number > $min && $number < $max;
    }

}


if ( ! function_exists('length'))
{

    /**
     * Get the length of a scalar|array|Countable
     */
    function length(mixed $value): int
    {

        if ( ! is_scalar($value) && ! is_countable($value))
        {
            throw new TypeError(sprintf('object of type %s has no length.', get_debug_type($value)));
        }

        switch (get_debug_type($value))
        {

            case 'bool':
                return $value ? 1 : 0;
            case 'int':
            case 'float':
                return $value > 0 ? (int) $value : 0;
            case 'string':
                return $value === '' ? 0 : mb_strlen($value);
        }

        return count($value);
    }

}
