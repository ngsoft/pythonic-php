<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Enums;

abstract class PHP
{

    /**
     * @link https://www.php.net/manual/en/language.oop5.magic.php
     */
    public const MAGIC_METHODS = [
        '__construct', '__destruct', '__call', '__callStatic',
        '__get', '__set', '__isset', '__unset',
        '__sleep', '__wakeup', '__serialize', '__unserialize',
        '__toString', '__invoke', '__set_state', '__clone', '__debugInfo',
    ];

    /**
     * Gep PHP internal classes
     */
    public static function getBuiltinInterfaces(): array
    {

        static $cache = [];

        if ( ! $cache)
        {
            foreach (get_declared_interfaces() as $class)
            {
                $reflector = new \ReflectionClass($class);
                if ($reflector->isInternal())
                {
                    $cache[$class] = $class;
                }
            }

            sort($cache);
        }

        return $cache;
    }

    /**
     * Get PHP Builtin methods
     */
    public static function getBuiltinMethods(): array
    {

        static $cache = [];

        if ( ! $cache)
        {

            $cache = array_combine(static::MAGIC_METHODS, static::MAGIC_METHODS);

            foreach (static::getBuiltinInterfaces() as $class)
            {
                foreach (get_class_methods($class) as $method)
                {
                    $cache[$method] = $method;
                }
            }

            sort($cache);
        }

        return $cache;
    }

}
