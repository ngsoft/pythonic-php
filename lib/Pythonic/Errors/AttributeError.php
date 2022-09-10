<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class AttributeError extends Error
{

    public static function of(string $attribute): static
    {
        return static::message('%s: %s', static::classname(), $attribute);
    }

    public static function raiseForAttribute(string $attribute)
    {

        throw static::of($attribute);
    }

}
