<?php

declare(strict_types=1);

namespace Pythonic;

use Pythonic\{
    Errors\TypeError, Utils\Importer
};
use function get_debug_type;

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

    if ( ! is_countable($countable))
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


        if (Typing\Types::checkType($object, $type))
        {
            return true;
        }
    }

    return false;
}
