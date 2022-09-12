<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class NoneType extends Type
{

    public function test(mixed $value): bool
    {
        return is_null($value);
    }

}
