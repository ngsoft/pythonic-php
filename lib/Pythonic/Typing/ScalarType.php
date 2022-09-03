<?php

declare(strict_types=1);

namespace Pythonic\Typing;

abstract class ScalarType extends Type
{

    protected ?string $name = null;

    public function name(): string
    {
        return $this->name ??= mb_strtolower(preg_replace('#Type$#', '', static::classname()));
    }

    public function alias(): string
    {
        return $this->name();
    }

}
