<?php

declare(strict_types=1);

namespace Pythonic\Typing;

abstract class ScalarType extends Type
{

    public function name(): string
    {
        if ($this->__name__ === '' && str_ends_with(static::class(), 'Type'))
        {
            $this->__name__ = mb_strtolower(mb_substr(self::classname(), 0, -4));
        }

        return parent::name();
    }

}
