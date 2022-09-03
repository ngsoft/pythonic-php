<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class BoolType extends ScalarType
{

    public function test(mixed $value): bool
    {

        return is_bool($value);
    }

}
