<?php

declare(strict_types=1);

namespace Pythonic\Errors;

trait ErrorHelper
{

    /**
     * Default error message
     * Overrides this to change it
     */
    protected static string $__default__ = '';

    /**
     * Override this to construct message programmatically
     */
    protected static function __message__(string $message): string
    {
        return $message === '' ? static::$__default__ : $message;
    }

    /**
     * Uses vprintf to format the exception
     * @phan-suppress PhanPluginPrintfVariableFormatString
     */
    public static function message(string $message, mixed ...$values)
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
    public static function raise(string $message, mixed ...$values): never
    {
        throw new static(static::message($message, ...$values));
    }

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {

        if ( ! ($this instanceof \Throwable))
        {

            return;
        }

        parent::__construct(static::__message__($message), $code, $previous);
    }

}
