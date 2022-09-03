<?php

declare(strict_types=1);

namespace Pythonic\Typing;

class NotImplementedType extends Type
{

    protected $alias = 'NotImplemented';

    public function test(mixed $value): bool
    {

        return $value === $this->name() || $value === $this->alias() || $value === __CLASS__;
    }

}
