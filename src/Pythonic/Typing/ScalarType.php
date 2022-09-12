<?php

declare(strict_types=1);

namespace Pythonic\Typing;

abstract class ScalarType extends Type
{

    protected static function scalarname(): string
    {
        if (str_ends_with($name = static::classname(), 'Type'))
        {
            $name = mb_strtolower(mb_substr($name, 0, -4));
        }

        return $name;
    }

    public function name(): string
    {
        return $this->name ??= static::scalarname();
    }

    public function alias(): string|array
    {
        return $this->alias ??= static::scalarname();
    }

}
