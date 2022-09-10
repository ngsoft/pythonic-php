<?php

declare(strict_types=1);

namespace Pythonic\Macros;

use Pythonic\Errors\AttributeError;

class HasMacros
{

    /**
     * @var Macro[]
     */
    protected static array $__macros__ = [];

    /**
     * Mix another object into the class
     */
    public static function mixin(object $other, bool $replace = true): void
    {
        foreach (Macro::fromObject($other) as $name => $macro)
        {

            if ($replace)
            {
                static::$__macros__[$name] = $macro;
                continue;
            }

            static::$__macros__[$name] ??= $macro;
        }
    }

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
            AttributeError::raiseForClassAttribute(static::class, $name);
        }
        return static::$__macros__[$name];
    }

    protected static function getMacro(string $name): Macro
    {

        if ( ! isset(static::$__macros__[$name]) || static::$__macros__[$name]->isStatic())
        {
            AttributeError::raiseForClassAttribute(static::class, $name);
        }
        return static::$__macros__[$name];
    }

    public function __call(string $name, array $arguments)
    {

        $macro = static::getMacro($name);
        return $macro($this, $arguments);
    }

    public static function __callStatic(string $name, array $arguments)
    {

        $macro = static::getStaticMacro($name);

        return $macro(static::class, $arguments);
    }

}
