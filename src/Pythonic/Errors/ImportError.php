<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class ImportError extends Error
{

    protected function __default__(): string
    {
        return 'Cannot import module';
    }

}
