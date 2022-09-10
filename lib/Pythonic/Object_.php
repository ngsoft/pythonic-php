<?php

declare(strict_types=1);

namespace Pythonic;

use ArrayAccess;
use Pythonic\{
    Enums\PHP, Errors\AttributeError, Errors\NotImplementedError, Utils\AttributeReader
};
use ReflectionClass,
    ReflectionProperty;

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
     * @access protected
     */
    public static function __dir__(object $self): array
    {

        static $hideMethods, $cache = [];

        $hideMethods ??= PHP::getBuiltinMethods();

        if ( ! ($self instanceof self))
        {
            NotImplementedError::raise('%s does not implements %s', get_class($self), __CLASS__);
        }

        $class = get_class($self);

        if ( ! isset($cache[$class]))
        {
            $cache[$class] = [];
            $result = &$cache[$class];

            foreach (get_class_methods($self) as $method)
            {

                if (in_array($method, $hideMethods) && ! AttributeReader::getMethodAttributes($self, IsPythonic::class))
                {
                    continue;
                }


                $result[$method] = $method;
            }

            // public properties
            /** @var ReflectionProperty $property */
            foreach ((new ReflectionClass($self))->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
            {

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
