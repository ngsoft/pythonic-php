<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class ValueError extends \ValueError implements PythonicError
{

    use \NGSOFT\Pythonic\Traits\ErrorHelper;

    /**
     * @phan-suppress PhanPluginAlwaysReturnMethod
     */
    public static function raiseForType(string $expected, string $got): never
    {

        static::raise('Value error: expected type %s, got %s.', $expected, $got);
    }

}
