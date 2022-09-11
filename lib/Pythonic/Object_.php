<?php

declare(strict_types=1);

namespace Pythonic;

use Closure,
    ErrorException;
use Pythonic\{
    Enums\PHP, Errors\AttributeError, Errors\TypeError, Traits\ClassUtils, Typing\Types, Utils\AttributeReader, Utils\Reflection, Utils\Utils
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
     * Public properties
     */
    protected array $__dict__ = [];

    /**
     * Dunder methods
     */
    protected array $__methods__ = [];

    /**
     * Reserved slots
     *
     * @var array|null
     */
    protected $__slots__ = null;

    #[Property]
    protected function __class__(): string
    {
        return sprintf('<class \'%s\'>', static::class());
    }

    /**
     * @return string[]
     */
    #[IsBuiltin]
    protected function __dir__(): iterable
    {

        static $hideMethods, $cache = [];

        $hideMethods ??= PHP::getBuiltinMethods();

        $class = static::class();

        if ( ! isset($cache[$class]))
        {

            $cache[$class] = $this->__methods__;
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
    protected function __repr__(): string
    {
        return sprintf('<%s object>', static::classname());
    }

    #[IsBuiltin]
    protected function __delattr__(string $name): void
    {


        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__delete__($this);
        }
        elseif ($name === '__slots__' && null !== $this->__slots__)
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
        elseif (method_exists($this, $name) && ! $this->hasSlot($name))
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
        elseif ($this->__isset($name))
        {
            unset($this->__dict__[$name]);
        }
        else
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }
    }

    #[IsBuiltin]
    protected function __setattr__(string $name, mixed $value): void
    {
        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__set__($this, $value);
        }
        elseif ($name === '__slots__' && null !== $this->__slots__)
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
        elseif (method_exists($this, $name) && ! $this->hasSlot($name))
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
        elseif ($this->hasSlot($name))
        {

            if (is_callable($value))
            {

                $value = $value instanceof Closure ? $value : Closure::fromCallable($value);
                try
                {
                    Utils::errors_as_exception();
                    $value = $value->bindTo($this, static::class());
                }
                catch (ErrorException)
                {

                }
                finally
                {
                    restore_error_handler();
                }
            }


            $this->__dict__[$name] = $value;
        }
        else
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }
    }

    #[IsBuiltin]
    protected function __getattribute__(string $name): mixed
    {
        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            return $property->__get__($this);
        }
        elseif ($name === '__dict__')
        {
            return $this->__dict__;
        }
        elseif ($this->hasSlot($name) && $this->__isset($name))
        {
            return $property;
        }
        else
        {
            return AttributeError::raiseForClassAttribute($this, $name);
        }
    }

    public function __construct()
    {

        static $cache = [];

        if ( ! isset($cache[static::class]))
        {

            $cache[static::class] = [
                '__dict__' => $this->__dict__,
                '__methods__' => $this->__methods__
            ];

            $dict = &$cache[static::class]['__dict__'];
            $methods = &$cache[static::class]['__methods__'];

            /** @var Property $instance */
            foreach (Property::of($this) as $prop => $instance)
            {
                $dict[$prop] = $instance;
            }

            if (null !== $this->__slots__)
            {
                $dict['__slots__'] = $this->__slots__;
            }

            // reserve slots, if not already
            foreach ($this->__slots__ ?? [] as $prop)
            {
                $dict[$prop] ??= null;
            }

            // add dunder method to __call
            foreach (Reflection::getMethods($this) as $reflectionMethod)
            {

                if ($reflectionMethod->isStatic() || $reflectionMethod->isPublic() || ! is_dunder($method = $reflectionMethod->getName()))
                {
                    continue;
                }

                $methods[$method] = $method;
            }
        }

        foreach ($cache[static::class] as $prop => $value)
        {

            $this->{$prop} = $value;
        }
    }

    protected function getMethodRepr(string $method): string
    {

        try
        {
            if ($method === '__repr__')
            {
                return sprintf('<method-wrapper %s of %s object>', $method, static::classname());
            }
            // dynamic methods __dict__[$method] will throw TypeError
            if (AttributeReader::getMethodAttribute($this, $method, IsBuiltin::class))
            {
                return sprintf('<built-in method %s of %s object>', $method, static::classname());
            }
        }
        catch (TypeError)
        {

        }

        return sprintf('<bound method %s::%s of %s>', static::class, $method, $this->__repr__());
    }

    protected function hasSlot(string $name): bool
    {

        if ( ! Utils::is_arrayaccess($this->__slots__))
        {
            return true;
        }

        return in_array($name, $this->__slots__);
    }

    public function __get(string $name): mixed
    {
        try
        {
            return $this->__getattribute__($name);
        }
        catch (AttributeError $error)
        {

            if (method_exists($this, $name) || is_callable($this->__dict__[$name] ?? null))
            {

                return $this->getMethodRepr($name);
            }

            throw $error;
        }
    }

    final public function __set(string $name, mixed $value): void
    {
        $this->__setattr__($name, $value);
    }

    final public function __unset(string $name): void
    {
        $this->__delattr__($name);
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->__dict__);
    }

    final public function __call(string $name, array $arguments): mixed
    {

        // public call to protected methods

        if (isset($this->__methods__[$name]))
        {
            return $this->{$name}(...$arguments);
        }


        // call to closures

        $property = $this->__getattribute__($name);

        if (is_callable($property))
        {
            return $property(...$arguments);
        }

        return TypeError::raise("'%s' object is not callable.", static::classname(Types::getType($property)));
    }

}
