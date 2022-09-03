<?php

declare(strict_types=1);

namespace Pythonic\Traits;

use Pythonic\Utils\Utils,
    Throwable;

trait ErrorHelper
{

    use ClassUtils;

    /**
     * Default error message
     * Overrides this to change it
     *
     * @var string[]
     */
    protected static array $__default__ = [];

    /**
     * Override this to set a default error message
     */
    protected function __default__(): string
    {
        $message = ltrim(preg_replace('#([A-Z])#', ' $1', static::classname()));

        return mb_strtoupper($message[0]) . mb_strtolower(mb_substr($message, 1));
    }

    /**
     * Override this to construct message programmatically
     */
    protected function __message__(string $message): string
    {
        if ($message !== '')
        {
            return $message;
        }

        return static::$__default__[static::class] ??= $this->__default__();
    }

    /**
     * Uses vsprintf to format the exception
     * @phan-suppress PhanPluginPrintfVariableFormatString
     */
    public static function printf(string $message, mixed ...$values): string
    {
        if (count($values))
        {
            $message = vsprintf($message, $values);
        }
        return $message;
    }

    /**
     * Creates a new instance using formatted message
     */
    public static function message(string $message, mixed ...$values): static
    {
        // intercept variadic previous and code

        [$prev, $code] = [Utils::pull($values, 'previous'), Utils::pull($values, 'code', 0)];

        var_dump($prev, $code, $values);

        return new static(static::printf($message, ...$values), $code, $prev);
    }

    /**
     * Throw error directly
     */
    public static function raise(string $message = '', mixed ...$values): never
    {
        throw static::message($message, ...$values);
    }

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {

        if ( ! ($this instanceof Throwable))
        {
            return;
        }

        parent::__construct($this->__message__($message), $code, $previous);
    }

}
