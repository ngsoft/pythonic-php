<?php

declare(strict_types=1);

namespace Pythonic\Typing;

abstract class ScalarType extends Type
{

    public static function __alias__(): string
    {

        return self::__name__();
    }

}
