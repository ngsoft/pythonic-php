<?php

declare(strict_types=1);

namespace Pythonic\Typing;

use Pythonic\__Object__;

class ObjectType extends ScalarType
{

    public function test(mixed $value): bool
    {

        return $value instanceof __Object__;
    }

}
