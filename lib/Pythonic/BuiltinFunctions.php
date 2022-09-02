<?php

declare(strict_types=1);

namespace Pythonic;

/**
 * Pythonic Builtin functions
 */
final class BuiltinFunctions implements Types\BootAble
{

    use Traits\NotInstanciable,
        Traits\Singleton;

    protected static $__slots__ = [];
    protected static $__all__ = [];
    protected static bool $__booted__ = false;

    /**
     * Loads Builtin functions into memory
     */
    public static function __boot__(): void
    {

        if (self::$__booted__)
        {
            return;
        }

        self::$__booted__ = true;

        $instance = self::instance();

        foreach (self::$__all__ as $method)
        {

        }
    }

}
