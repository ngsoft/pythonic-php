<?php

declare(strict_types=1);

namespace Pythonic;

function from(string $namespace): Importer
{
    return Importer::from($namespace);
}

function import(string $resource): mixed
{
    return Importer::import($resource);
}
