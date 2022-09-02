<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class RuntimeException extends \RuntimeException implements PythonicError
{

    use ErrorHelper;
}
