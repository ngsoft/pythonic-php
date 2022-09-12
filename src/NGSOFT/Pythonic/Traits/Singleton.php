<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Traits;

use Pythonic\Errors\{
    AttributeError, RuntimeError
};

/**
 * Use Singleton/Facade antipattern to call instances methods statically
 */
trait Singleton
{

    /**
     * @var object[]
     */
    protected static array $__instance__ = [];

    /**
     * The class to instanciate
     * Override this to instanciate another class
     *
     * @var ?string
     */
    protected static $__class__ = null;

    /**
     * Instanciate the unique instance
     * Override this to add arguments if needed
     *
     * @phan-suppress PhanUndeclaredMethod
     */
    protected static function __instanciate__(): object
    {

        $class = static::$__class__ ?? static::class;

        if (trait_exists($class) || interface_exists($class) || ! class_exists($class))
        {
            RuntimeError::raise('Cannot instanciate %s.', $class);
        }
        return new $class();
    }

    /**
     * Access to the unique instance
     */
    public static function instance(): object
    {
        return static::$__instance__[static::class] ??= static::__instanciate__();
    }

    protected static function executeMethod(object $self, string $method, array $arguments): mixed
    {
        if ( ! method_exists($self, $method))
        {
            AttributeError::raiseForClassAttribute(get_class($self), $method);
        }
        return $self->{$method}(...$arguments);
    }

    /**
     * Translates static call into instance call
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        // call $class::__method__ as $class::instance()->method()
        return static::executeMethod(static::instance(), $name, $arguments);
    }

}
