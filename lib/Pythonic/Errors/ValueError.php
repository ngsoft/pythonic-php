<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class ValueError extends \ValueError implements PythonicError
{

    use ErrorHelper;
}
