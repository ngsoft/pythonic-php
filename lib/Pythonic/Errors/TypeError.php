<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class TypeError extends \TypeError implements PythonicError
{

    use ErrorHelper;

    public static function raiseArgumentCountError(string $resource, int $expected, int $len): never
    {
        throw static::message('%s expected %d, got %s', $resource, $expected, $len);
    }

}
