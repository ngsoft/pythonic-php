<?php

declare(strict_types=1);

namespace Pythonic;

/**
 * Pythonic Builtin functions
 */
class BuiltinFunctions implements Types\BootAble
{

    use Traits\NotInstanciable;

    protected static $__slots__ = [];
    protected static $__all__ = [];

    /**
     * Loads Builtin functions into memory
     */
    public static function __boot__(): void
    {

    }

}
