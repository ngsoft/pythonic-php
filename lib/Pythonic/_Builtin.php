<?php

declare(strict_types=1);

namespace Pythonic;

/**
 * Replaces the python from keyword
 * usage $resource = from('namespace.subnamespace')->import('name')
 */
function from(string $namespace): \Pythonic\Utils\Importer
{
    return Utils\Importer::from($namespace);
}

/**
 * imports resource(s) that can be a function name or class name
 */
function import(string|array $resource, &$as = null, ?string $from = null): string|array
{
    return Utils\Importer::import($resource, $as, $from);
}
