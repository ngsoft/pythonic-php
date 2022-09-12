<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class IntType extends ScalarType
{

    public function test(mixed $value): bool
    {
        return is_int($value);
    }

}
