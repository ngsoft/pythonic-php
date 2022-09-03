<?php

declare(strict_types=1);

namespace Pythonic\Traits;

trait ErrorHelper
{

    /**
     * Default error message
     * Overrides this to change it
     *
     * @var string
     */
    protected $__default__ = '';

    /**
     * Override this to construct message programmatically
     */
    protected function __message__(string $message): string
    {
        return $message === '' ? $this->__default__ : $message;
    }

    /**
     * Uses vsprintf to format the exception
     * @phan-suppress PhanPluginPrintfVariableFormatString
     */
    public static function printf(string $message, mixed ...$values): string
    {
        unset($values['previous']);
        if (count($values))
        {
            $message = vsprintf($message, $values);
        }
        return $message;
    }

    /**
     * Creates a new instance using formatted message
     */
    public static function message(string $message, mixed ...$values)
    {
        // intercept variadic previous: $error
        $prev = $values['previous'] ?? null;
        return new static(static::printf($message, ...$values), previous: $prev);
    }

    /**
     * Throw error directly
     */
    public static function raise(string $message = '', mixed ...$values): never
    {
        throw static::message($message, ...$values);
    }

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {

        if ( ! ($this instanceof \Throwable))
        {
            return;
        }

        parent::__construct($this->__message__($message), $code, $previous);
    }

}
