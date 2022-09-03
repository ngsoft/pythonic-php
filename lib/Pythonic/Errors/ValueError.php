<?php

declare(strict_types=1);

namespace Pythonic\Errors;

use Pythonic\Traits\ErrorHelper;

class ValueError extends \ValueError implements PythonicError
{

    use ErrorHelper;
}
