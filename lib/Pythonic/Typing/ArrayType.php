<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class ArrayType extends Type
{

    public function alias(): string
    {
        return 'array';
    }

    public function test(mixed $value): bool
    {
        return is_array($value);
    }

}
