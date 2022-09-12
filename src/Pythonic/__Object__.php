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
        return static::class;
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



        $prop = $this->__dict__[$name];
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
        // property cache
        static $properties = [];
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

        return $this->hasAttr($name);
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
