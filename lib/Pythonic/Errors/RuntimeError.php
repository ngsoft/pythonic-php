<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class RuntimeError extends \RuntimeException implements PythonicError
{

    use ErrorHelper;
}
