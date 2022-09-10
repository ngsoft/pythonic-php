<?php

declare(strict_types=1);

namespace Pythonic;

use Pythonic\{
    Enums\PHP, Errors\AttributeError, Traits\ClassUtils, Utils\AttributeReader, Utils\Reflection
};

/**
 * The base python object
 * All Pythonic classes extends this one
 *
 * Object is a PHP reserved keyword
 */
class Object_
{

    use ClassUtils;

    /**
     * Reserved slots
     */
    protected array $__dict__ = [];
    protected ?array $__slots__ = null;
    protected ?string $__class__ = null;

    #[Property, IsBuiltin]
    public function __class__(): string
    {
        return $this->__class__ ??= sprintf('<class \'%s\'>', static::class);
    }

    /**
     * @return string[]
     */
    #[IsBuiltin]
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

    #[IsBuiltin]
    public function __repr__(): string
    {
        return sprintf('<%s object>', static::classname());
    }

    public function __construct()
    {

        /** @var Property $instance */
        foreach (Property::of($this) as $prop => $instance)
        {
            $this->__dict__[$prop] = $instance;
        }
    }

    protected function getMethodRepr(string $method): string
    {

        if ($method === '__repr__')
        {
            return sprintf('<method-wrapper %s of %s object>', $method, static::classname());
        }

        if (AttributeReader::getMethodAttribute($this, $method, IsBuiltin::class))
        {
            return sprintf('<built-in method %s of %s object>', $method, static::classname());
        }

        return sprintf('<bound method %s::%s of %s>', static::class, $method, $this->__repr__());
    }

    protected function hasSlot(string $name): bool
    {

        if (null === $this->__slots__)
        {
            return true;
        }

        return in_array($name, $this->__slots__);
    }

    public function __get(string $name): mixed
    {

        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            return $property->__get__($this);
        }
        elseif (method_exists($this, $name))
        {
            return $this->getMethodRepr($name);
        }
        elseif ($this->hasSlot($name))
        {
            return $property;
        }
        else
        {
            return AttributeError::raiseForClassAttribute($this, $name);
        }
    }

    public function __set(string $name, mixed $value): void
    {

        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__set__($this, $value);
        }
        elseif ($this->hasSlot($name) && ! method_exists($this, $name))
        {
            $this->__dict__[$name] = $value;
        }
        else
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }
    }

    public function __unset(string $name): void
    {
        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__delete__($this);
            return;
        }
        elseif ($this->hasSlot($name) && ! method_exists($this, $name))
        {
            unset($this->__dict__[$name]);
            return;
        }
        else
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }
    }

    public function __isset(string $name): bool
    {
        return ! is_null($this->__dict__[$name] ?? null);
    }

}
