<?php

declare(strict_types=1);

namespace Pythonic\Utils;

use Pythonic\Errors\TypeError,
    ReflectionClass,
    ReflectionException;

/**
 * @phan-file-suppress PhanPluginAlwaysReturnMethod
 */
final class Reflection
{

    use \Pythonic\Traits\NotInstanciable;

    /**
     * Get Reflector for class
     *
     * @param string|object $class
     * @return ReflectionClass
     */
    public static function getClass(string|object $class): ReflectionClass
    {

        try
        {
            if ( ! is_string($class))
            {
                $class = get_class($class);
            }

            $result = new ReflectionClass($class);
        }
        catch (ReflectionException)
        {
            TypeError::raise('class %s does not exists.', $class);
        }

        return $result;
    }

    /**
     * Get Subclasses of class including itself
     *
     * @param string|object $class
     * @return ReflectionClass[]
     */
    public static function getSubClasses(string|object $class): iterable
    {


        try
        {
            if ( ! is_string($class))
            {
                $class = get_class($class);
            }

            $result = [];

            while (false !== $class)
            {
                $result[$class] = static::getClass($class);
                $class = get_parent_class($class);
            }
        }
        catch (TypeError | ReflectionException)
        {
            TypeError::raise('class %s does not exists.', $class);
        }

        return $result;
    }

    /**
     * Get Property
     */
    public static function getProperty(string|object $class, string $property): \ReflectionProperty
    {


        if ( ! is_string($class))
        {
            $class = get_class($class);
        }


        /** @var ReflectionClass $reflectionClass */
        foreach (self::getSubClasses($class) as $reflectionClass)
        {

            if ($reflectionClass->hasProperty($property))
            {
                return $reflectionClass->getProperty($property);
            }
        }


        TypeError::raise('class %s does not have property %s.', $class, $property);
    }

    public static function getMethod(string|object $class, string $method): \ReflectionMethod
    {


        if ( ! is_string($class))
        {
            $class = get_class($class);
        }


        /** @var ReflectionClass $reflectionClass */
        foreach (self::getSubClasses($class) as $reflectionClass)
        {

            if ($reflectionClass->hasMethod($method))
            {
                return $reflectionClass->getMethod($method);
            }
        }


        TypeError::raise('class %s does not have method %s.', $class, $method);
    }

}
