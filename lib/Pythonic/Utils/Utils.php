<?php

declare(strict_types=1);

namespace Pythonic\Utils;

use ArrayAccess,
    Countable,
    Pythonic\Errors\TypeError,
    Stringable,
    Throwable;
use function array_is_list,
             get_debug_type;

/**
 * Modded methods found in ngsoft/tools, illuminate/support
 */
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

    /**
     * Checks if number is between min and max
     */
    public static function in_range(int|float $number, int|float $min, int|float $max, bool $inclusive = true): bool
    {


        if ($min === $max)
        {
            return $number === $min && $inclusive;
        }

        // swap arguments
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

    /**
     * Get the length of a scalar|array|Countable
     */
    public static function length(mixed $value): int
    {

        if ( ! is_scalar($value) && ! is_countable($value))
        {
            TypeError::raise('object of type %s has no length.', get_debug_type($value));
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

    /**
     * Checks if value is not negative
     */
    public static function is_unsigned(int|float $value): bool
    {
        return $value >= 0;
    }

    /**
     * Check if value is Array like
     */
    public static function is_arrayaccess(mixed $value): bool
    {

        if (is_array($value))
        {
            return true;
        }


        return $value instanceof ArrayAccess && $value instanceof Countable;
    }

    /**
     * Checks if value can be converted to string
     */
    public static function is_stringable(mixed $value): bool
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

    /**
     * Get string value of a variable
     */
    public static function strval(mixed $value): string
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

        // prevents another method call
        if (is_numeric($value))
        {
            return (string) $value;
        }


        // checks $value->__toString()
        if ( ! static:: is_stringable($value))
        {
            TypeError::raise('value of type %s is not stringable.', get_debug_type($value));
        }


        return (string) $value;
    }

    /**
     * Checks if value is a sequence
     */
    public static function is_sequence(mixed $value): bool
    {

        if ( ! is_iterable($value) && ! static::is_arrayaccess($value))
        {
            return false;
        }


        if (is_array($value))
        {
            return array_is_list($value);
        }

        // array|Traversable
        if (is_iterable($value))
        {
            $nextKey = -1;

            foreach ($value as $k => $_)
            {
                if ($k !== ++ $nextKey)
                {
                    return false;
                }
            }

            return true;
        }

        // ArrayAccess&Countable
        for ($offset = 0; $offset < count($value); $offset ++ )
        {

            try
            {
                // isset can return false negative isset(0), isset(false): we want isset(null)
                if ($value[$offset] === null)
                {
                    return false;
                }
            }
            // offsetGet can throw exceptions depending implementation
            catch (Throwable)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Count number of occurences of value inside iterable
     */
    public static function count_value(mixed $value, iterable $iterable): int
    {
        $cnt = 0;

        foreach ($iterable as $_value)
        {
            if ($_value === $value)
            {
                $cnt ++;
            }
        }
        return $cnt;
    }

    /**
     * Pull value from array like and returns it
     *
     * @phan-suppress PhanUnusedVariable, PhanRedundantCondition
     */
    public static function pull(ArrayAccess|array &$array, mixed $offset, mixed $default = null): mixed
    {

        $valid = true;

        if (is_array($array) && ! is_int($offset) && ! is_string($offset))
        {
            TypeError::raise('array only accept offsets of type string|int, got %s', get_debug_type($offset));
        }


        try
        {

            if (is_null($value = $array[$offset] ?? null))
            {
                return $default;
            }

            return $value;
        }
        catch (\Throwable)
        {
            $valid = false;
            return $default;
        }
        finally
        {
            if ($valid)
            {
                unset($array[$offset]);
            }
        }
    }

    /**
     * Get user defined constants
     */
    public static function get_user_defined_constants(): array
    {
        return get_defined_constants(true)['user'] ?? [];
    }

}
