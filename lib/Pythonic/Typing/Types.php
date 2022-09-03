<?php

declare(strict_types=1);

namespace Pythonic\Typing;

use Pythonic\{
    Errors\TypeError, Traits\NotInstanciable
};
use ReflectionClass;
use function get_debug_type;

final class Types
{

    use NotInstanciable;

    private const BUILTIN_TYPES = [
        // php compat
        ArrayType::class,
        // primitives
        StringType::class,
        FloatType::class,
        IntType::class,
        BoolType::class,
        NoneType::class,
        // first one to check (overrides string type)
        NotImplementedType::class,
    ];

    static protected array $__mappings__ = [];
    static protected array $__defined__ = [];

    public static function __boot__(): void
    {

        if ( ! self::$__mappings__)
        {

            foreach (self::BUILTIN_TYPES as $type)
            {
                self::register($type);
            }
        }

        var_dump([self::$__mappings__, self::$__defined__]);
    }

    protected static function isValidType(string $type): bool
    {
        static $cache = [];

        return $cache[$type] ??= is_subclass_of($type, Type::class, true) && ! (new ReflectionClass($type))->isAbstract();
    }

    /**
     * Add a Type
     */
    public static function register(string|Type $type): void
    {

        if (is_object($type))
        {
            $type = get_class($type);
        }

        if ( ! self::isValidType($type))
        {
            TypeError::raise('invalid type %s', $type);
        }


        if (in_array($type, self::$__mappings__))
        {
            return;
        }


        [$name, $alias] = [$type::__name__(), $type::__alias__()];

        // inserts custom types before builtin types
        self::$__mappings__ = [$name => $type] + self::$__mappings__;

        self::$__defined__[$alias] = $name;

        if ( ! defined($alias))
        {
            define($alias, $name);
        }
    }

    /**
     * Get Type from value
     */
    public static function getType(mixed $value): Type
    {

        /** @var Type $type */
        foreach (self::$__mappings__ as $type)
        {

            if ($type::__test__($value))
            {
                return $type::instance();
            }
        }

        throw TypeError::message('invalid type %s', get_debug_type($value));
    }

    /**
     * Checks if value matches input type
     */
    public static function checkType(mixed $value, string $type): bool
    {
        $type = self::$__defined__[$type] ?? $type;
        $type = self::$__mappings__ [$type] ?? $type;

        if ( ! self::isValidType($type))
        {
            TypeError::raise('invalid type %s', $type);
        }

        return $type::__test__($value);
    }

    /**
     * same as checkType but without raising error
     */
    public static function isType(mixed $value, string $type): bool
    {

        try
        {
            return self::checkType($value, $type);
        }
        catch (TypeError)
        {
            return false;
        }
    }

    public static function __defined__(): array
    {
        return self::$__defined__;
    }

}
