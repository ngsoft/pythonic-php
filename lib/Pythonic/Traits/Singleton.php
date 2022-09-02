<?php

declare(strict_types=1);

namespace Pythonic\Traits;

use Pythonic\Errors\{
    NotImplementedError, RuntimeError
};
use ReflectionException,
    ReflectionMethod;

/**
 * Use Singleton/Facade antipattern to call instances methods statically
 */
trait Singleton
{

    /**
     * @var ?object
     */
    protected static ?object $__instance__ = null;

    /**
     * The class to instanciate
     * Override this to instanciate another class
     *
     * @var string
     */
    protected static ?string $__class__ = null;

    /**
     * Instanciate the unique instance
     * Override this to add arguments if needed
     *
     * @phan-suppress PhanUndeclaredMethod
     */
    protected static function __instanciate__(): object
    {

        $class = self::$__class__ ??= static::class;

        if (trait_exists($class) || interface_exists($class))
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
        return self::$__instance__ ??= self::__instanciate__();
    }

    protected static function executeMethod(object $self, string $method, array $arguments): mixed
    {

        try
        {
            return (new ReflectionMethod($self, $method))->invokeArgs($self, $arguments);
        } catch (ReflectionException $prev)
        {
            return NotImplementedError::raise('%s::%s() is not implemented.', static::class, $method, previous: $prev);
        }
    }

    /**
     * Translates static call into instance call
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        // call $class::__method__ as $class::instance()->method()
        return static::executeMethod(static::instance(), preg_replace('#^_{2}(.+)_{2}$#', '$1', $name), $arguments);
    }

}
