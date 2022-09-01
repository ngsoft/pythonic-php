<?php

declare(strict_types=1);

namespace py;

use ArrayAccess,
    Countable,
    Throwable,
    TypeError;
use function array_is_list,
             get_debug_type;

/**
 * Checks if value is a list
 */
function is_list(mixed $value): bool
{

    // mixed union, intersection type check
    if (
            ! is_iterable($value) &&
            ! ($value instanceof ArrayAccess && $value instanceof Countable)
    )
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

    if (count($value) === 0)
    {
        return true;
    }


    for ($offset = 0; $offset < count($value); $offset ++ )
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

function isinstance(mixed $object, string|array ...$types): bool
{

}
