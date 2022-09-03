<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class NotImplementedError extends RuntimeError
{

    protected function __default__(): string
    {
        return 'Not implemented';
    }

}
