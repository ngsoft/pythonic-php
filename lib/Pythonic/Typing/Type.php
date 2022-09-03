<?php

declare(strict_types=1);

namespace Pythonic\Typing;

use Pythonic\Traits\{
    ClassUtils, NotInstanciable, Singleton
};

abstract class Type
{

    use Singleton,
        NotInstanciable,
        ClassUtils;

    /** @var ?string */
    protected $__name__ = null;

    /**
     * Get type name
     */
    public static function __name__(): string
    {
        return static::instance()->__name__ ??= static::instance()->name();
    }

    /**
     * Test if value is type
     */
    public static function __test__(mixed $value): bool
    {
        return static::instance()->test($value);
    }

    /**
     * Alias to use for Type constant
     */
    public static function __alias__(): string
    {
        return static::instance()->alias();
    }

    /**
     * Get the named type
     */
    public function name(): string
    {
        return static::classname();
    }

    /**
     * get aliased name
     */
    public function alias(): string
    {
        return static::classname();
    }

    /**
     * Test type
     */
    abstract public function test(mixed $value): bool;
}
