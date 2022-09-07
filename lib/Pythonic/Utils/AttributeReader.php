<?php

declare(strict_types=1);

namespace Pythonic\Utils;

abstract class AttributeReader
{

    /**
     * Returns the first named class attribute
     */
    public static function getClassAttribute(string|object $class, string $attribute): ?object
    {


        try
        {

            /** @var \ReflectionClass $reflectionClass */
            /** @var \ReflectionAttribute $reflectionAttribute */
            foreach (Reflection::getSubClasses($class) as $reflectionClass)
            {

                foreach ($reflectionClass->getAttributes() as $reflectionAttribute)
                {
                    if ($reflectionAttribute->getName() === $attribute)
                    {
                        return $reflectionAttribute->newInstance();
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
    public static function getMethodAttributes(string|object $class, string $attribute): iterable
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
                    catch (\Throwable)
                    {

                    }
                }
            }
        }


        yield from $methods;
    }

    /**
     * Get all properties first named attribute
     */
    public static function getPropertyAttributes(string|object $class, string $attribute): iterable
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
                    catch (\Throwable)
                    {

                    }
                }
            }
        }


        yield from $properties;
    }

}
