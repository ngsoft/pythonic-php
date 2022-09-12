<?php

declare(strict_types=1);

namespace Pythonic\Errors;

use Pythonic\Traits\ErrorHelper;

class Exception extends \Exception implements PythonicError
{

    use ErrorHelper;
}
