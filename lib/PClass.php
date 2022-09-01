<?php

declare(strict_types=1);

namespace Python;

class PClass
{

    protected function __repr__(): string
    {
        return 'new ' . static::class . '()';
    }

}
