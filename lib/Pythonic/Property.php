<?php

declare(strict_types=1);

namespace Pythonic;

use Attribute,
    Closure,
    Pythonic\Errors\TypeError,
    ReflectionClass,
    ReflectionException,
    ReflectionFunction,
    ReflectionMethod;
use const None;
use function array_is_list;

/**
 * The python property
 * use this as attribute to retain "@property"
 * this can also be used inside your constructor for protected or lower properties
 * eg: $this->prop = new Property('getProp', 'setProp')
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Property
{

    protected ?string $fget = None;
    protected ?string $fset = None;
    protected ?string $fdel = None;
    protected ?string $name = None;

    /**
     * Scan for all attributes and return type for class and returns instances
     */
    public static function of(string|object $class): array
    {

        if ( ! is_string($class))
        {
            $class = get_class($class);
        }

        if ( ! class_exists($class))
        {
            TypeError::raise('invalid class %s', $class);
        }


        $instances = [];

        $reflClass = new ReflectionClass($class);

        /** @var \ReflectionProperty $reflector */
        foreach ($reflClass->getProperties() as $reflector)
        {

            $name = $reflector->getName();

            // we check for attribute first
            /** @var \ReflectionAttribute $attribute */
            foreach ($reflector->getAttributes() as $attribute)
            {
                if ($attribute->getName() === __CLASS__)
                {


                    $prop = $attribute->newInstance();

                    if ($prop->name !== '')
                    {
                        $name = $prop->name;
                    }

                    $instances[$name] = $prop;
                    continue 2;
                }
            }



            if ($reflector->isPrivate())
            {
                continue;
            }


            // we check return type is exactly __CLASS__
            // not nullable and not union/intersection: those ones are to be implemented in the constructor

            if ($reflector->hasType() && ! $reflector->hasDefaultValue() && (string) $reflector->getType() === __CLASS__)
            {
                // getter, setter, deleter are to be set
                $prop = new static(isAttribute: false);

                if ($prop->name !== '')
                {
                    $name = $prop->name;
                }

                $instances[$name] = $prop;
            }
        }


        // transform a method as a getter

        /** @var \ReflectionMethod $reflector */
        foreach ($reflClass->getMethods() as $reflector)
        {

            $method = $name = $reflector->getName();

            foreach ($reflector->getAttributes() as $attribute)
            {
                if ($attribute->getName() === __CLASS__)
                {
                    $instance = $attribute->newInstance();

                    if ( ! $instance->fget)
                    {
                        $instance->getter($method);
                    }

                    if ($instance->name !== '')
                    {
                        $name = $instance->name;
                    }

                    $instances[$name] = $instance;

                    break;
                }
            }
        }

        return $instances;
    }

    public function __construct(
            ?string $fget = None,
            ?string $fset = None,
            ?string $fdel = None,
            ?string $name = None,
            public readonly bool $isAttribute = true
    )
    {

        $this->name = $name;

        $fget && $this->getter($fget);
        $fset && $this->setter($fset);
        $fdel && $this->deleter($fdel);
    }

    public function getter(string $fget): static
    {
        $this->fget = $fget;
        return $this;
    }

    public function setter(string $fset): static
    {
        $this->fset = $fset;
        return $this;
    }

    public function deleter(string $fdel): static
    {
        $this->fdel = $fdel;
        return $this;
    }

    protected function getCallable(object $obj, string $method): callable
    {

        $callable = [$obj, $method];
        if ( ! is_callable($callable))
        {
            TypeError::raise('object %s method %s is not accessible.', get_class($obj), $method);
        }

        return $callable;
    }

    public function __get__(object $obj): mixed
    {

        if ( ! $this->fget)
        {
            return None;
        }
        return call_user_func($this->getCallable($obj, $this->fget));
    }

    public function __set__(object $obj, mixed $value): void
    {

        if ( ! $this->fset)
        {
            return;
        }

        call_user_func($this->get_callable($obj, $this->fset), $value);
    }

    public function __delete__(object $obj): void
    {

        if ( ! $this->fdel)
        {
            return;
        }

        call_user_func($this->get_callable($obj, $this->fdel));
    }

}
