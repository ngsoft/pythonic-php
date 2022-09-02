<?php

declare(strict_types=1);

use Pythonic\Utils\Importer;

$__all__ = [
];

if ( ! function_exists('import'))
{

    function import(string $resource): mixed
    {
        return Importer::import($resource);
    }

}


if ( ! function_exists('from'))
{

    function from(string $namespace): Importer
    {
        return Importer::from($namespace);
    }

}






from('utils'); $importer = import('importer');

var_dump($importer);

var_dump($importer::instance());
