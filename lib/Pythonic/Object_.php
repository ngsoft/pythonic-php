<?php

declare(strict_types=1);

namespace Pythonic;

use ArrayAccess;
use Pythonic\{
    Enums\PHP, Errors\AttributeError, Utils\Reflection
};

/**
 * The base python object
 * All Pythonic classes extends this one
 *
 * Object is a PHP reserved keyword
 */
class Object_
{

    protected array|ArrayAccess $__dict__ = [];

    /**
     *
     */
    public function __dir__(): iterable
    {

        static $hideMethods, $cache = [];

        $hideMethods ??= PHP::getBuiltinMethods();

        $class = get_class($this);

        if ( ! isset($cache[$class]))
        {
            $cache[$class] = [];
            $result = &$cache[$class];

            foreach (Reflection::getMethods($this) as $reflectionMethod)
            {

                if ( ! $reflectionMethod->isPublic() || $reflectionMethod->isStatic())
                {
                    continue;
                }

                $method = $reflectionMethod->getName();

                if (in_array($method, $hideMethods) && ! $reflectionMethod->getAttributes(IsPythonic::class))
                {
                    continue;
                }


                $result[$method] = $method;
            }

            // public properties

            foreach (Reflection::getProperties($this) as $reflectionProperty)
            {
                if ( ! $reflectionProperty->isPublic() && ! $reflectionProperty->isStatic())
                {
                    continue;
                }

                $property = $reflectionProperty->getName();
                $result[$property] = $property;
            }

            foreach (array_keys($this->__dict__) as $attr)
            {
                $result[$attr] = $attr;
            }


            $result = array_values($result);
        }




        return $cache[$class];
    }

    public function __construct()
    {

        if (static::class === __CLASS__)
        {
            return;
        }

        /** @var Property $instance */
        foreach (Property::of($this) as $prop => $instance)
        {
            $this->__dict__[$prop] = $instance;
        }
    }

    public function __get(string $name): mixed
    {

        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            return $property->__get__($this);
        }

        return AttributeError::raise('attribute %s does not exists.', $name);
    }

    public function __set(string $name, mixed $value): void
    {

        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__set__($this, $value);
        }

        AttributeError::raise('attribute %s does not exists.', $name);
    }

    public function __unset(string $name): void
    {
        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__delete__($this);
        }

        AttributeError::raise('attribute %s does not exists.', $name);
    }

    public function __isset(string $name): bool
    {
        return ! is_null($this->__dict__[$name] ?? null);
    }

}
