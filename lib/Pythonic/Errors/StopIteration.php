<?php

declare(strict_types=1);

namespace Pythonic\Errors;

class StopIteration extends Exception
{

    protected $__default = 'Iteration has stopped';

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
