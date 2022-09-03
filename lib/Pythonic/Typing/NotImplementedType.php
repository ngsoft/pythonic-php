<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class NotImplementedType extends Type
{

    protected $__name__ = 'NotImplemented';

    public function test(mixed $value): bool
    {

        return $value === __CLASS__;
    }

}
