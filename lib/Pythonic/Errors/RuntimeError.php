<?php

declare(strict_types=1);

namespace Pythonic\Errors;

use Pythonic\Traits\ErrorHelper,
    RuntimeException;

class RuntimeError extends RuntimeException implements PythonicError
{

    use ErrorHelper;
}
