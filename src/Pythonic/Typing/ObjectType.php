<?php

declare(strict_types=1);

namespace Pythonic\Typing;

use Pythonic\Object_;

class ObjectType extends ScalarType
{

    public function test(mixed $value): bool
    {

        return $value instanceof Object_;
    }

}
