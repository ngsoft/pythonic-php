<?php

declare(strict_types=1);

namespace Pythonic;

use Pythonic\{
    Errors\TypeError, Typing\Types, Utils\Importer, Utils\StrUtils, Utils\Utils
};
use function get_debug_type,
             str_ends_with,
             str_starts_with;

/**
 * Replaces the python from keyword
 * usage $resource = from('namespace.subnamespace')->import('name')
 */
function from(string $namespace): Importer
{
    return Importer::from($namespace);
}

/**
 * imports resource(s) that can be a function name or class name
 */
function import(string|array $resource, &$as = null, ?string $from = null): string|array
{
    return Importer::import($resource, $as, $from);
}

/**
 * Alias of count
 */
function len(mixed $countable): int
{

    if (is_string($countable))
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
        TypeError::raiseArgumentCountError('isinstance', 2, 1);
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

        if ( ! class_exists($type) && interface_exists($type) && ! trait_exists($type))
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
function is_sunder(string $value): bool
{
    return
            len($value) > 2 &&
            $value[0] === '_' &&
            $value[1] !== '_' &&
            $value[-1] === '_' &&
            $value[-2] !== '_';
}

/**
 * Returns True if a '__dunder__' name, False otherwise.
 */
function is_dunder(string $value): bool
{
    return
            len($value) > 4 &&
            $value[0] . $value[1] === '__' &&
            $value[-1] . $value[-2] === '__' &&
            $value[2] !== '_' &&
            $value[-3] !== '_';
}
