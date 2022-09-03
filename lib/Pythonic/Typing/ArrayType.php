<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class ArrayType extends Type
{

    protected $name = 'array';

    public function test(mixed $value): bool
    {
        return is_array($value);
    }

}
