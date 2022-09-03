<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class FloatType extends ScalarType
{

    public function test(mixed $value): bool
    {

        return is_float($value);
    }

}
