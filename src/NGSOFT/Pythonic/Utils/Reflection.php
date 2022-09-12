<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Utils;

use Pythonic\Errors\TypeError,
    ReflectionClass,
    ReflectionException,
    ReflectionMethod,
    ReflectionProperty;

/**
 * @phan-file-suppress PhanPluginAlwaysReturnMethod, PhanPossiblyUndeclaredVariable
 */
abstract class Reflection
{

    /**
     * Get Reflector for class
     *
     * @param string|object $class
     * @return ReflectionClass
     */
    public static function getClass(string|object $class): ReflectionClass
    {


        static $cache = [];

        if ( ! is_string($class))
        {
            $class = get_class($class);
        }

        if ( ! isset($cache[$class]))
        {
            try
            {
                $cache[$class] = new ReflectionClass($class);
            }
            catch (ReflectionException)
            {
                TypeError::raise('class %s does not exists.', $class);
            }
        }

        return $cache[$class];
    }

    /**
     * Get Subclasses of class including itself
     *
     * @param string|object $class
     * @return ReflectionClass[]
     */
    public static function getSubClasses(string|object $class)
    {
        static $cache = [];

        if ( ! is_string($class))
        {
            $class = get_class($class);
        }

        $cache[$class] ??= [];

        if ( ! $cache[$class])
        {
            try
            {

                $result = &$cache[$class];
                $parent = $class;
                while (false !== $parent)
                {
                    $result[$parent] = static::getClass($parent);
                    $parent = get_parent_class($parent);
                }
            }
            catch (TypeError | ReflectionException)
            {
                TypeError::raise('class %s does not exists.', $parent);
            }
        }

        return $cache[$class];
    }

    /**
     * Get Property
     */
    public static function getProperty(string|object $class, string $property): ReflectionProperty
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

    /**
     * Get all class and subclasses properties
     *
     * @param string|object $class
     * @return ReflectionProperty[]
     */
    public static function getProperties(string|object $class)
    {

        static $cache = [];

        if ( ! is_string($class))
        {
            $class = get_class($class);
        }


        $cache[$class] ??= [];

        if ( ! $cache[$class])
        {

            $properties = &$cache[$class];

            /** @var ReflectionClass $reflectionClass */
            /** @var ReflectionProperty $reflectionProperty */
            foreach (static::getSubClasses($class) as $reflectionClass)
            {

                foreach ($reflectionClass->getProperties() as $reflectionProperty)
                {
                    $name = $reflectionProperty->getName();
                    if (isset($properties[$name]))
                    {
                        continue;
                    }


                    $properties[$name] = $reflectionProperty;
                }
            }
        }

        return $cache[$class];
    }

    /**
     * Get Method
     */
    public static function getMethod(string|object $class, string $method): ReflectionMethod
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

    /**
     * Get all class and subclasses methods
     *
     * @param string|object $class
     * @return ReflectionMethod[]
     */
    public static function getMethods(string|object $class)
    {
        static $cache = [];

        if ( ! is_string($class))
        {
            $class = get_class($class);
        }


        $cache[$class] ??= [];

        if ( ! $cache[$class])
        {

            $methods = &$cache[$class];

            /** @var ReflectionClass $reflectionClass */
            /** @var $reflectionMethod $reflectionMethod */
            foreach (static::getSubClasses($class) as $reflectionClass)
            {

                foreach ($reflectionClass->getMethods() as $reflectionMethod)
                {
                    $name = $reflectionMethod->getName();

                    if (isset($methods[$name]))
                    {
                        continue;
                    }


                    $methods[$name] = $reflectionMethod;
                }
            }
        }

        return $cache[$class];
    }

}
