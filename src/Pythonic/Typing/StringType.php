<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class StringType extends ScalarType
{

    protected $alias = ['string', 'str'];

    public function test(mixed $value): bool
    {

        if (is_object($value))
        {
            return method_exists($value, '__toString');
        }

        return is_string($value);
    }

}
