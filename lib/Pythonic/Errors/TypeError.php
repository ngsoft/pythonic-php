<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class TypeError extends \TypeError implements PythonicError
{

    use ErrorHelper;
}
