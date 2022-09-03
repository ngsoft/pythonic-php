<?php

declare(strict_types=1);

namespace Pythonic\Types;

abstract class Type
{

    protected string $__name__ = '';

    public function name(): string
    {

        return $name;
    }

}
