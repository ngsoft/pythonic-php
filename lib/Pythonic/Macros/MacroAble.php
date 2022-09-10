<?php

declare(strict_types=1);

namespace Pythonic\Macros;

use Pythonic\Traits\ClassUtils;

class MacroAble
{

    /**
     * @var Macro[]
     */
    protected static array $__macros__ = [];

    /**
     * Erase all macros
     */
    public static function flushMacros(): void
    {
        static::$__macros__ = [];
    }

    /**
     * Checks if macro exists
     */
    public static function hasMacro(string $name): bool
    {
        return isset(static::$__macros__[$name]);
    }

    /**
     * Adds a macro if it does not already exists
     */
    public static function addMacro(string $name, callable $macro, bool $isStatic = false): void
    {

        if ( ! static::hasMacro($name))
        {
            static::setMacro($name, $macro, $isStatic);
        }
    }

    /**
     * Set a macro
     */
    public static function setMacro(string $name, callable $macro, bool $isStatic = false): void
    {
        static::$__macros__[$name] = Macro::fromCallable($macro, $name, $isStatic);
    }

    protected static function getStaticMacro(string $name): Macro
    {


        if ( ! isset(static::$__macros__[$name]) || ! static::$__macros__[$name]->isStatic())
        {

        }
    }

    public function __call(string $name, array $arguments)
    {

    }

    public static function __callStatic(string $name, array $arguments)
    {

    }

}
