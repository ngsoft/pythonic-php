<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class StopIteration extends Exception
{

    protected function __default__(): string
    {
        return 'Iteration has stopped';
    }

}
