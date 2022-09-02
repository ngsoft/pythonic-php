<?php

declare(strict_types=1);

namespace Pythonic\Utils;

trait IsBootAble
{

    protected static array $__all__ = [];

    public function __all__(): array
    {
        return static::$__all__;
    }

}
