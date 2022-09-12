<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class Error extends \Error implements PythonicError
{

    use \NGSOFT\Pythonic\Traits\ErrorHelper;
}
