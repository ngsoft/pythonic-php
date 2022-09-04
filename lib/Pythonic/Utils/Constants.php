<?php

declare(strict_types=1);

namespace Pythonic\Utils;

use Pythonic\Traits\{
    NotInstanciable, Singleton
};

/**
 * Manages User defined constants
 */
class Constants
{

    use Singleton,
        NotInstanciable;

    protected array $__ignored__ = [];

    public static function __callStatic(string $name, array $arguments): mixed
    {

    }

}
