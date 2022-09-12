<?php

declare(strict_types=1);

namespace Pythonic\Errors;

use Pythonic\Traits\ErrorHelper;

class TypeError extends \TypeError implements PythonicError
{

    use ErrorHelper;

    /**
     * @phan-suppress PhanPluginAlwaysReturnMethod
     */
    public static function raiseArgumentCountError(string $resource, int $expected, int $len): never
    {
        static::raise('%s expected %d, got %s', $resource, $expected, $len);
    }

}
