<?php

declare(strict_types=1);

namespace Pythonic\Types;

use Pythonic\{
    Errors\NameError, Traits\NotInstanciable, Traits\Singleton
};

abstract class Type
{

    use Singleton,
        NotInstanciable;

    protected string $__name__ = '';

    /**
     * Get type name
     */
    public static function __name__(): string
    {
        return static::instance()->name();
    }

    public function __test__(mixed $value): bool
    {
        return static::instance()->test($value);
    }

    /**
     * Get the named type
     */
    public function name(): string
    {

        if ($this->__name__ === '')
        {
            NameError::raise();
        }

        return $this->__name__;
    }

    /**
     * Test type
     */
    abstract public function test(mixed $value): bool;
}
