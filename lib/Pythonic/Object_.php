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
     * Reserved slots
     */
    protected $__slots__ = null;

    /**
     * __class__() caches
     */
    protected ?string $__class__ = null;

    #[Property]
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

        if (null !== $this->__slots__)
        {
            $this->__dict__['__slots__'] = $this->__slots__;
        }

        // reserve slots, if not already
        foreach ($this->__slots__ ?? [] as $prop)
        {
            $this->__dict__[$prop] ??= null;
        }


        $this->test = pass(...);
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
        elseif ($name === '__dict__')
        {
            return $this->__dict__;
        }
        elseif ($this->hasSlot($name) && $this->__isset($name))
        {
            return $property;
        }
        elseif (method_exists($this, $name))
        {
            return $this->getMethodRepr($name);
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
        }
        elseif ($name === '__slots__' && null !== $this->__slots__)
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
        elseif (method_exists($this, $name) && ! $this->hasSlot($name))
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
        elseif (null !== $property)
        {
            unset($this->__dict__[$name]);
        }
        else
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->__dict__);
    }

    public function __call(string $name, array $arguments): mixed
    {

        if ( ! $this->__isset($name))
        {
            return AttributeError::raiseForClassAttribute($this, $name);
        }


        $property = $this->__dict__[$name] ?? null;

        if (is_callable($property))
        {


            $closure = $property instanceof Closure ? $property : Closure::fromCallable($property);

            try
            {
                Utils::errors_as_exception();
                $closure = $closure->bindTo($this, static::class());
            }
            catch (ErrorException)
            {

            }
            finally
            {
                restore_error_handler();
            }

            return $closure(...$arguments);
        }

        return TypeError::raise("'%s' object is not callable.", static::classname(Types::getType($property)));
    }

}
