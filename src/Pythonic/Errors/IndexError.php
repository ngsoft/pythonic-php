<?php

declare(strict_types=1);

namespace Pythonic\Errors;

/**
 * @phan-file-suppress PhanPluginAlwaysReturnMethod
 */
class IndexError extends Error
{

    public static function raiseInvalidOffset(int $offset, int $max, int $min = 0): never
    {

        static::raise('Invalid offset %d, not in range (%d, %d)', $offset, $min, $max);
    }

}
