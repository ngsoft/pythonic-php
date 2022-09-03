<?php

declare(strict_types=1);

namespace Pythonic\Errors;

use Pythonic\Traits\ErrorHelper;

class Error extends \Error implements PythonicError
{

    use ErrorHelper;
}
