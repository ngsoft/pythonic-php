<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

use NGSOFT\Pythonic\Utils\Reflection,
    Throwable;

abstract class Reader
{

    /**
     * Returns the first named class attribute
     */
    public static function getClassAttribute(string|object $class, string $attribute): ?object
    {
        /** @var \ReflectionClass $reflectionClass */
        /** @var \ReflectionAttribute $reflectionAttribute */
        foreach (Reflection::getSubClasses($class) as $reflectionClass)
        {

            foreach ($reflectionClass->getAttributes() as $reflectionAttribute)
            {
                if ($reflectionAttribute->getName() === $attribute)
                {
                    try
                    {
                        return $reflectionAttribute->newInstance();
                    }
                    catch (Throwable)
                    {

                    }
                }
            }
        }

        return null;
    }

    /**
     * Get first named attribute for a class method
     */
    public static function getMethodAttribute(string|object $class, string $method, string $attribute): ?object
    {

        $reflector = Reflection::getMethod($class, $method);

        /** @var \ReflectionAttribute $reflectionAttribute */
        foreach ($reflector->getAttributes() as $reflectionAttribute)
        {

            if ($reflectionAttribute->getName() === $attribute)
            {

                try
                {
                    return $reflectionAttribute->newInstance();
                }
                catch (Throwable)
                {

                }
            }
        }


        return null;
    }

    /**
     * Get all methods first named attribute
     */
    public static function getMethodsAttributes(string|object $class, string $attribute): iterable
    {



        $methods = [];

        /** @var \ReflectionMethod $reflectionMethod */
        /** @var \ReflectionAttribute $reflectionAttribute */
        foreach (Reflection::getMethods($class) as $reflectionMethod)
        {


            $method = $reflectionMethod->getName();
            if (isset($methods[$method]))
            {
                continue;
            }

            foreach ($reflectionMethod->getAttributes() as $reflectionAttribute)
            {

                if ($reflectionAttribute->getName() === $attribute)
                {

                    try
                    {
                        $methods[$method] = $reflectionAttribute->newInstance();
                        break;
                    }
                    catch (Throwable)
                    {

                    }
                }
            }
        }


        return $methods;
    }

    /**
     * Get first named attribute for a class property
     */
    public static function getPropertyAttribute(string|object $class, string $property, string $attribute): ?object
    {

        $reflector = Reflection::getProperty($class, $property);

        /** @var \ReflectionAttribute $reflectionAttribute */
        foreach ($reflector->getAttributes() as $reflectionAttribute)
        {

            if ($reflectionAttribute->getName() === $attribute)
            {

                try
                {
                    return $reflectionAttribute->newInstance();
                }
                catch (Throwable)
                {

                }
            }
        }


        return null;
    }

    /**
     * Get all properties first named attribute
     */
    public static function getPropertiesAttributes(string|object $class, string $attribute): iterable
    {


        $properties = [];

        /** @var \ReflectionProperty $reflectionProperty */
        /** @var \ReflectionAttribute $reflectionAttribute */
        foreach (Reflection::getProperties($class) as $reflectionProperty)
        {


            $property = $reflectionProperty->getName();
            if (isset($properties[$property]))
            {
                continue;
            }

            foreach ($reflectionProperty->getAttributes() as $reflectionAttribute)
            {

                if ($reflectionAttribute->getName() === $attribute)
                {
                    try
                    {
                        $properties[$property] = $reflectionAttribute->newInstance();
                        break;
                    }
                    catch (Throwable)
                    {

                    }
                }
            }
        }


        return $properties;
    }

}
