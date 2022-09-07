<?php

declare(strict_types=1);

namespace Pythonic;

use ArrayAccess;
use const None;

/**
 * The base python object
 * All Pythonic classes extends this one
 *
 * Object is a PHP reserved keyword
 */
class Object_
{

    protected array|ArrayAccess $__dict__ = [];

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
    }

    public function __set(string $name, mixed $value): void
    {

        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__set__($this, $value);
        }
    }

    public function __unset(string $name): void
    {
        $property = $this->__dict__[$name] ?? null;

        if ($property instanceof Property)
        {
            $property->__delete__($this);
        }
    }

}
