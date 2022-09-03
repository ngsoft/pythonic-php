<?php

declare(strict_types=1);

use Pythonic\Utils\Importer;

if ( ! defined('NAMESPACE_SEPARATOR'))
{
    define('NAMESPACE_SEPARATOR', '\\');
}


if ( ! function_exists('import'))
{

    /**
     * Imports a resource that can be a function name or class name
     */
    function import(string|array $resource, &$as = null, ?string $from = null): string|array
    {
        return Importer::import($resource, $as, $from);
    }

}


if ( ! function_exists('from'))
{

    /**
     * Set the namespace for imported resources
     */
    function from(string $namespace): Importer
    {
        return Importer::from($namespace);
    }

}


require_once __DIR__ . '/Pythonic/builtin.php';

Pythonic\Typing\Types::boot();

var_dump(constant('string'));
