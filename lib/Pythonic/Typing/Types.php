<?php

declare(strict_types=1);

namespace Pythonic\Typing;

use Pythonic\{
    Errors\TypeError, Traits\ClassUtils
};
use const NAMESPACE_SEPARATOR;
use function get_debug_type,
             str_ends_with;

final class Types
{

    static protected array $__all__ = [];

    public static function boot(): void
    {

        static $booted = false;

        if ( ! $booted)
        {
            $booted = true;

            foreach (scandir(__DIR__) ?: [] as $file)
            {

                if ( ! str_ends_with($file, '.php'))
                {
                    continue;
                }



                if (in_array($file, ['Type.php', 'Types.php', 'ScalarType.php']))
                {
                    continue;
                }



                $class = __NAMESPACE__ . NAMESPACE_SEPARATOR . substr($file, 0, - 4);
                $path = __DIR__ . DIRECTORY_SEPARATOR . $file;

                if ( ! self::isValidType($class))
                {
                    continue;
                }

                self::addType($class);
            }
        }
    }

    protected static function isValidType(string $type): bool
    {
        static $cache = [];

        return $cache[$type] ??= is_subclass_of($type, Type::class, true) && ! (new \ReflectionClass($type))->isAbstract();
    }

    /**
     * Add a Type
     */
    public static function addType(string|Type $type): void
    {

        if (is_object($type))
        {
            $type = get_class($type);
        }

        if ( ! self::isValidType($type))
        {
            TypeError::raise('invalid type %s', $type);
        }

        $name = $type::__name__();

        if (isset(self::$__all__[$name]))
        {
            return;
        }

        self::$__all__[$name] = $type;

        if ( ! defined($name))
        {
            define($name, $type::__alias__());
        }
    }

    /**
     * Get Type from value
     */
    public function getType(mixed $value): Type
    {

        /** @var Type $type */
        foreach (self::$__all__ as $type)
        {

            if ($type::__test__($value))
            {
                return $type::instance();
            }
        }

        throw TypeError::message('invalid type %s', get_debug_type($value));
    }

    /**
     * Checks if type mathes inputed type
     */
    public function checkType(mixed $value, string $type): bool
    {
        $type = self::$__all__[$type] ?? $type;

        return $this->getType($value) === $type;
    }

}
