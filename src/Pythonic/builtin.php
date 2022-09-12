<?php

declare(strict_types=1);

namespace Pythonic;

use NGSOFT\Pythonic\Utils\{
    StrUtils, Utils
};
use Pythonic\{
    Errors\TypeError, Typing\Types
};
use function get_debug_type;

/**
 * Pass
 */
function pass()
{
    return;
}

/**
 * Alias of count
 */
function len(mixed $countable): int
{

    if (is_object($countable) && method_exists($countable, '__len__'))
    {
        return $countable->__len__();
    }
    elseif (is_string($countable))
    {
        return StrUtils::len($countable);
    }
    elseif ( ! is_countable($countable))
    {
        TypeError::raise('object of type %s has no len()', get_debug_type($countable));
    }
    return count($countable);
}

/**
 * Return whether an object is an instance of a class or of a subclass thereof.
 */
function issubclass(mixed $object, string ...$types): bool
{
    if ( ! is_object($object))
    {
        return false;
    }

    return isinstance($object, ...$types);
}

/**
 * Return whether an object is an instance of a class or of a subclass thereof.
 */
function isinstance(mixed $object, string ...$types): bool
{

    if (count($types) === 0)
    {
        TypeError::raiseArgumentCountError(__FUNCTION__, 2, 1);
    }


    foreach ($types as $type)
    {

        // pythonic types
        if (Types::isType($object, $type))
        {
            return true;
        }


        // scalar types and called class
        if (get_debug_type($object) === $type)
        {
            return true;
        }


        // fallback to subclassof
        if ( ! is_object($object))
        {
            continue;
        }

        if ( ! class_exists($type) && ! interface_exists($type) && ! trait_exists($type))
        {
            continue;
        }


        if (is_a($object, $type))
        {
            return true;
        }


        if (Utils::uses_trait($object, $type))
        {
            return true;
        }
    }

    return false;
}

/**
 * Returns True if a '_sunder_' name, False otherwise.
 */
function is_sunder(mixed $value): bool
{
    $value = Utils::strval($value);
    return
            len($value) > 2 &&
            mb_substr($value, 0, 1) === '_' &&
            mb_substr($value, -1) === '_' &&
            mb_substr($value, 1, 1) !== '_' &&
            mb_substr($value, -2, 1) !== '_';
}

/**
 * Returns True if a '__dunder__' name, False otherwise.
 */
function is_dunder(mixed $value): bool
{
    $value = Utils::strval($value);
    return
            len($value) > 4 &&
            mb_substr($value, 0, 2) === '__' &&
            mb_substr($value, -2) === '__' &&
            mb_substr($value, 2, 1) !== '_' &&
            mb_substr($value, -3, 1) !== '_';
}
