<?php

declare(strict_types=1);

namespace Pythonic\Errors;

use RuntimeException;

class RuntimeError extends RuntimeException implements PythonicError
{

    use \NGSOFT\Pythonic\Traits\ErrorHelper;
}
