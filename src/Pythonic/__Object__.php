<?php

declare(strict_types=1);

namespace Pythonic;

use Closure,
    ErrorException;
use NGSOFT\Pythonic\{
    Traits\ClassUtils, Utils\Utils
};
use Pythonic\{
    Errors\AttributeError, Errors\TypeError, Typing\Types
};

/**
 * The Base pythonic object
 */
class __Object__
{

    use ClassUtils;

    protected $__slots__ = null;
    protected array $__dict__ = [];

    ////////////////////////////   Pythonic Methods   ////////////////////////////


    #[Property]
    protected function __class__(): string
    {
        return static::classname();
    }

    protected function __getattribute__(string $name): mixed
    {

        if ($name === '__dict__')
        {
            return $this->__dict__;
        }

        if ( ! array_key_exists($name, $this->__dict__))
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }

        $value = $this->__dict__[$name];

        if ($value instanceof Property)
        {
            return $value->__get__($this);
        }
        return $value;
    }

    protected function __setattr__(string $name, mixed $value): void
    {
        $this->assertSlotReadOnly($name);

        if ( ! $this->hasSlot($name))
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }


        if ($value instanceof Closure)
        {

            try
            {
                Utils::errors_as_exception();
                $value = $value->bindTo($this, static::class);
            }
            catch (ErrorException)
            {

            }
            finally
            {
                restore_error_handler();
            }
        }

        $prop = $this->__dict__[$name] ?? null;

        if ($prop instanceof Property)
        {
            $prop->__set__($this, $value);
        }
        else
        {
            $this->__dict__[$name] = $value;
        }
    }

    protected function __delattr__(string $name): void
    {
        $this->assertSlotReadOnly($name);

        if ( ! array_key_exists($name, $this->__dict__))
        {
            AttributeError::raiseForClassAttribute($this, $name);
        }

        $prop = $this->__dict__[$name];

        if ($prop instanceof Property)
        {
            $prop->__delete__($this);
        }
        else
        {
            unset($this->__dict__[$name]);
        }
    }

    ////////////////////////////   PHP Magics   ////////////////////////////


    public function __construct()
    {


        // properties cache
        static $properties = [];

        $properties[static::class] = Property::of($this);

        $this->__dict__ = $properties[static::class];

        // initialize slots
        foreach ($this->__slots__ ?? [] as $slot)
        {
            if ($slot === '__dict__')
            {
                continue;
            }
            $this->__dict__[$slot] ??= null;
        }


        if (null !== $this->__slots__)
        {
            $this->__dict__['__slots__'] = $this->__slots__;
        }

        // protected dunder methods (for __call)
        foreach (get_class_methods($this) as $method)
        {
            // not static __method__ for getter (faster than reflection)
            if (is_dunder($method) && ! is_callable(sprintf('%s::%s', static::class, $method)))
            {
                // Closure or not ?
                $this->__dict__[$method] ??= [$this, $method];
            }
        }
    }

    public function __call(string $name, array $arguments): mixed
    {

        $attr = $this->__getattribute__($name);

        if ( ! is_callable($attr))
        {
            TypeError::raise("'%s' object is not callable.", static::classname(Types::getType($attr)));
        }

        return $attr(...$arguments);
    }

    public function __get(string $name): mixed
    {
        return $this->__getattribute__($name);
    }

    protected function assertSlotReadOnly(string $name): void
    {

        if ($name === '__slots__' && null !== $this->__slots__)
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
        elseif ( ! $this->hasSlot($name) && method_exists($this, $name))
        {
            AttributeError::raiseForReadOnlyAttribute($this, $name);
        }
    }

    protected function hasSlot(string $name): bool
    {

        if ($name === '__dict__')
        {
            return false;
        }

        if ( ! is_array($this->__slots__))
        {
            return true;
        }

        return in_array($name, $this->__slots__);
    }

    protected function hasAttr(string $name): bool
    {

        try
        {
            $this->__getattribute__($name);
            return true;
        }
        catch (AttributeError)
        {
            return false;
        }
    }

    public function __isset(string $name): bool
    {

        try
        {
            return $this->__getattribute__($name) !== null;
        }
        catch (AttributeError)
        {
            return false;
        }
    }

    public function __set(string $name, mixed $value): void
    {
        $this->__setattr__($name, $value);
    }

    public function __unset(string $name): void
    {
        $this->__delattr__($name);
    }

}
