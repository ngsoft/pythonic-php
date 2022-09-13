<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

use NGSOFT\Pythonic\Utils\Reflection,
    ReflectionAttribute,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty,
    Throwable;

abstract class Reader
{

    /**
     * Attribute instanciator
     */
    protected static function getInstance(
            ReflectionAttribute $reflector,
            ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionParameter|ReflectionProperty|ReflectionClassConstant $container
    ): object
    {

        $class = $reflector->getName();

        if (is_subclass_of($class, BaseAttribute::class, true))
        {

            return $class::fromReflectionAttribute($reflector, $container);
        }

        return $reflector->newInstance();
    }

    /**
     * Returns the first named class attribute
     */
    public static function getClassAttribute(string|object $class, string $attribute): ?object
    {

        try
        {
            /** @var ReflectionClass $reflectionClass */
            /** @var ReflectionAttribute $reflectionAttribute */
            foreach (Reflection::getSubClasses($class) as $reflectionClass)
            {

                foreach ($reflectionClass->getAttributes() as $reflectionAttribute)
                {
                    if ($reflectionAttribute->getName() === $attribute)
                    {
                        try
                        {
                            return self::getInstance($reflectionAttribute, $reflectionClass);
                        }
                        catch (Throwable)
                        {

                        }
                    }
                }
            }
        }
        catch (\Throwable)
        {

        }



        return null;
    }

    /**
     * Get first named attribute for a class method
     */
    public static function getMethodAttribute(string|object $class, string $method, string $attribute): ?object
    {

        try
        {
            $reflector = Reflection::getMethod($class, $method);

            /** @var ReflectionAttribute $reflectionAttribute */
            foreach ($reflector->getAttributes() as $reflectionAttribute)
            {

                if ($reflectionAttribute->getName() === $attribute)
                {

                    try
                    {
                        return self::getInstance($reflectionAttribute, $reflector);
                    }
                    catch (Throwable)
                    {

                    }
                }
            }
        }
        catch (\Throwable)
        {

        }






        return null;
    }

    /**
     * Get all methods first named attribute
     */
    public static function getMethodsAttributes(string|object $class, string $attribute): iterable
    {



        $methods = [];

        try
        {
            /** @var ReflectionMethod $reflectionMethod */
            /** @var ReflectionAttribute $reflectionAttribute */
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
                            $methods[$method] = self::getInstance($reflectionAttribute, $reflectionMethod);
                            break;
                        }
                        catch (Throwable)
                        {

                        }
                    }
                }
            }
        }
        catch (\Throwable)
        {

        }

        return $methods;
    }

    /**
     * Get first named attribute for a class property
     */
    public static function getPropertyAttribute(string|object $class, string $property, string $attribute): ?object
    {



        try
        {
            $reflector = Reflection::getProperty($class, $property);

            /** @var ReflectionAttribute $reflectionAttribute */
            foreach ($reflector->getAttributes() as $reflectionAttribute)
            {

                if ($reflectionAttribute->getName() === $attribute)
                {

                    try
                    {
                        return self::getInstance($reflectionAttribute, $reflector);
                    }
                    catch (Throwable)
                    {

                    }
                }
            }
        }
        catch (\Throwable)
        {

        }



        return null;
    }

    /**
     * Get all properties first named attribute
     */
    public static function getPropertiesAttributes(string|object $class, string $attribute): iterable
    {


        $properties = [];

        try
        {


            /** @var ReflectionProperty $reflectionProperty */
            /** @var ReflectionAttribute $reflectionAttribute */
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
                            $properties[$property] = self::getInstance($reflectionAttribute, $reflectionProperty);
                            break;
                        }
                        catch (Throwable)
                        {

                        }
                    }
                }
            }
        }
        catch (\Throwable)
        {

        }



        return $properties;
    }

    /**
     * Checks if class is attribute
     */
    public static function isAttribute(string|object $class): bool
    {


        if (is_object($class))
        {
            $class = get_class($class);
        }

        if ( ! class_exists($class))
        {
            return false;
        }


        if (static::getClassAttribute($class, \Attribute::class))
        {
            return true;
        }

        return false;
    }

    /**
     * Get attribute metadata
     */
    public static function getAttributeInfos(string|object $attribute): AttributeInfo
    {

        if ($attribute instanceof \ReflectionAttribute)
        {
            $attribute = $attribute->getName();
        }

        return AttributeInfo::of($attribute);
    }

}
