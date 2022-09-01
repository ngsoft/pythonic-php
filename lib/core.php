<?php

declare(strict_types=1);

use InvalidArgumentException,
    Throwable,
    TypeError;
use function array_is_list,
             get_debug_type,
             is_arrayaccess;

if ( ! function_exists('is_list'))
{

    /**
     * Checks if value is a list
     */
    function is_list(mixed $value): bool
    {

        // mixed union, intersection type check
        if ( ! is_iterable($value) && ! is_arrayaccess($value))
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

        if (count($value) === 0)
        {
            return true;
        }


        for ($offset = 0; $offset < count($value); $offset ++)
        {

            // isset can return false negative
            try
            {
                if ($value[$offset] === null)
                {
                    return false;
                }
            } catch (Throwable)
            {
                return false;
            }
        }


        return true;
    }

}

if ( ! function_exists('len'))
{

    /**
     * Alias of count
     */
    function len(mixed $countable): int
    {

        if ( ! is_countable($countable))
        {
            throw new TypeError(sprintf('object of type %s has no len()', get_debug_type($countable)));
        }
        return count($countable);
    }

}


if ( ! function_exists('isinstance'))
{

    /**
     * Return whether an object is an instance of a class or of a subclass thereof.
     */
    function isinstance(mixed $object, string ...$types): bool
    {

        if (empty($types))
        {
            throw new InvalidArgumentException('At least one type is required.');
        }


        foreach ($types as $type)
        {
            // scalar types and called class
            if (get_debug_type($object) === $type)
            {
                return true;
            }

            if ( ! is_object($object))
            {
                continue;
            }

            if ( ! class_exists($type) && interface_exists($type) && ! trait_exists($type))
            {
                continue;
            }


            if (is_a($object, $type))
            {
                return true;
            }


            if (uses_trait($object, $type))
            {
                return true;
            }
        }

        return false;
    }

}


if ( ! function_exists('issubclass'))
{

    /**
     * Return whether an object is an instance of a class or of a subclass thereof.
     */
    function issubclass(mixed $object, string ...$types): bool
    {
        if (empty($types))
        {
            throw new InvalidArgumentException('At least one type is required.');
        }
        if ( ! is_object($object))
        {
            return false;
        }

        return isinstance($object, ...$types);
    }

}


