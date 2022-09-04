<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class StringType extends ScalarType
{

    protected $alias = ['string', 'str'];

    public function test(mixed $value): bool
    {
        return is_string($value);
    }

}
