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


if ( ! function_exists('safe_import'))
{

    /**
     * Include file without overriding globals
     * @phan-suppress PhanImpossibleCondition
     */
    function safe_import(string $__file__, array $__data__ = []): mixed
    {
        extract($__data__);

        $__all__ = $__name__ = null;

        $result = require $__file__;

        if (isset($__all__) && isset($__name__))
        {
            foreach ((array) $__all__ as $resource)
            {

                Importer::alias($resource, sprintf('%s.%s', $__name__, $resource));
            }
        }

        return $__all__ ?? $result;
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
        safe_import(getcwd() . DIRECTORY_SEPARATOR . $resource . PHP_EXT);
    }
} finally
{
    if (isset($pwd))
    {
        chdir($pwd);
    }
}









