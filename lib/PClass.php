<?php

declare(strict_types=1);

namespace Python;

/**
 * All classes extends that one
 *
 */
class PClass
{

    final public function __set(string $name, mixed $value): void
    {
        $this->__setattr__($name, $value);
    }

    final public function __unset(string $name): void
    {
        $this->__delattr__($name);
    }

    final public function __get(string $name): mixed
    {

        return $this->__getattribute__($name);
    }

    final public function __isset(string $name): bool
    {

        try
        {
            return $this->__getattribute__($name) !== null;
        } catch (Throwable)
        {
            return false;
        }
    }

    protected function __setattr__(string $name, mixed $value): void
    {
        if ( ! property_exists($this, $name))
        {
            throw new AttributeError(sprintf("'%s' object has no attribute '%s'", static::class, $name));
        }

        $this->{$name} = $value;
    }

    protected function __delattr__(string $name): void
    {
        $this->__setattr__($name, null);
    }

    protected function __getattribute__(string $name): mixed
    {

        if ( ! property_exists($this, $name))
        {
            throw new AttributeError(sprintf("'%s' object has no attribute '%s'", static::class, $name));
        }

        return $this->{$name};
    }

    protected function __eq__(self $other): bool
    {
        return $other === $this;
    }

    protected function __ne__(self $other): bool
    {
        return ! $this->__eq__($other);
    }

    protected function __str__(): string
    {
        return '';
    }

    protected function __repr__(): string
    {
        return $this->__str__();
    }

    protected function __format__(string $format): string
    {
        return $format;
    }

}
