<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class NotImplementedType extends Type
{

    public static function __alias__(): string
    {
        return 'NotImplemented';
    }

    public function test(mixed $value): bool
    {

        return $value === __CLASS__;
    }

}
