<?php

declare(strict_types=1);

namespace Pythonic;

use Pythonic\Utils\Importer;

$__all__ = [
    'from',
    'import',
];

/**
 * Set the namespace for imported resources
 */
function from(string $namespace): Importer
{
    return Importer::from($namespace);
}

/**
 * Imports a resource that can be a function name or class name
 */
function import(string|array $resource, &$as = null, ?string $from = null): string|array
{
    return Importer::import($resource, $as, $from);
}
