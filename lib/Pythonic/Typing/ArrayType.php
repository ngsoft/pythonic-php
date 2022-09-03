<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class ArrayType extends Type
{

    public function name(): string
    {
        return 'array';
    }

    public function test(mixed $value): bool
    {
        return is_array($value);
    }

}
