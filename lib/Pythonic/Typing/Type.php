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

    protected string $__name__ = '';

    /**
     * Get type name
     */
    public static function __name__(): string
    {
        return static::instance()->name();
    }

    public static function __test__(mixed $value): bool
    {
        return static::instance()->test($value);
    }

    public static function __alias__(): string
    {
        return static::class;
    }

    /**
     * Get the named type
     */
    public function name(): string
    {

        if ($this->__name__ === '')
        {
            $this->__name__ = mb_strtolower(preg_replace('#Type$#', '', static::classname()));
        }

        return $this->__name__;
    }

    /**
     * Test type
     */
    abstract public function test(mixed $value): bool;
}
