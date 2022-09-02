<?php

declare(strict_types=1);

namespace Pythonic\Traits;

use Pythonic\Errors\RuntimeException;

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
     */
    protected static function __instanciate__(): object
    {

        $class = self::$__class__ ??= static::class;

        return new $class();
    }

    /**
     * Access to the unique instance
     */
    public static function instance(): object
    {
        return self::$__instance__ ??= self::__instanciate__();
    }

}
