<?php

declare(strict_types=1);

namespace Python;

class StopIteration extends \Exception
{

    public function __construct(string $message = "Iteration has stopped", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
