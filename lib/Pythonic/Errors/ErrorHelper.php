<?php

declare(strict_types=1);

namespace Pythonic\Errors;

trait ErrorHelper
{

    /**
     * Uses vprintf to format the exception
     */
    public static function vprintf(string $message, mixed ...$values)
    {


        if (count($values))
        {
            $message = vprintf($message, $values);
        }

        return new static($message);
    }

    /**
     * Throw error directly
     */
    public function raise(string $message, mixed ...$values): never
    {


        throw new static(static::vprintf($message));
    }

}
