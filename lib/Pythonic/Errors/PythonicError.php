<?php

declare(strict_types=1);

namespace Pythonic\Errors;

/**
 * Pythonic Namespace Error type check
 */
interface PythonicError extends \Throwable
{

    /**
     * Throw error directly
     */
    public static function raise(string $message = '', mixed ...$values): never;
}
