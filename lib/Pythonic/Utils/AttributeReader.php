<?php

declare(strict_types=1);

namespace Pythonic\Utils;

use Pythonic\Errors\TypeError,
    ReflectionClass,
    ReflectionException;

abstract class AttributeReader
{

    /**
     * Returns the first named class attribute
     */
    public static function getClassAttribute(string|object $class, string $name): ?object
    {

        foreach (Reflection::getSubClasses($class) as $reflectionClass)
        {

        }
    }

}
