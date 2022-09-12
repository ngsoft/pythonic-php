<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class TypeError extends \TypeError implements PythonicError
{

    use \NGSOFT\Pythonic\Traits\ErrorHelper;

    /**
     * @phan-suppress PhanPluginAlwaysReturnMethod
     */
    public static function raiseArgumentCountError(string $resource, int $expected, int $len): never
    {
        static::raise('%s expected %d, got %s', $resource, $expected, $len);
    }

}
