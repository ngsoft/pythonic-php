<?php

declare(strict_types=1);

use Pythonic\Utils\Importer;

$__all__ = [
    'builtin'
];

if ( ! defined('NAMESPACE_SEPARATOR'))
{
    define('NAMESPACE_SEPARATOR', '\\');
}

if ( ! defined('PHP_EXT'))
{
    define('PHP_EXT', '.php');
}


if ( ! function_exists('safe_include'))
{

    function safe_include(string $__file__, array $__data__ = []): mixed
    {
        extract($__data__);

        $result = require_once $__file__;

        if (isset($__name__))
        {
            Importer::alias($__name__);
        }

        if (isset($__all__))
        {
            return $__all__;
        }

        return $result;
    }

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




try
{

    $pwd = getcwd();
    chdir(__DIR__ . '/Pythonic');

    foreach ($__all__ as $resource)
    {
        safe_include(getcwd() . DIRECTORY_SEPARATOR . $resource . PHP_EXT);
    }
} finally
{
    chdir($pwd);
}









