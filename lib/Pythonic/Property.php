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

    protected $fget = None;
    protected $fset = None;
    protected $fdel = None;

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
            $fget = None,
            $fset = None,
            $fdel = None,
            public readonly string $name = '',
            public readonly bool $isAttribute = true
    )
    {

        $fget && $this->getter($fget);
        $fset && $this->setter($fset);
        $fdel && $this->deleter($fdel);
    }

    public function getter(array|string|Closure $fget): static
    {
        $this->fget = $fget;
        return $this;
    }

    public function setter(array|string|Closure $fset): static
    {
        $this->fset = $fset;
        return $this;
    }

    public function deleter(array|string|Closure $fdel): static
    {
        $this->fdel = $fdel;
        return $this;
    }

    /**
     * @phan-suppress PhanPluginAlwaysReturnMethod, PhanPossiblyUndeclaredVariable
     */
    protected function get_callable(object|string $obj, array|string|Closure $callable): ReflectionFunction|ReflectionMethod
    {

        try
        {
            $call = $callable;

            if ($call instanceof Closure)
            {
                return new ReflectionFunction($call);
            }

            if (is_string($call))
            {
                $call = [$obj, $call];
            }

            if ( ! array_is_list($call) || count($call) !== 2)
            {
                TypeError::raise('invalid callable');
            }

            return new ReflectionMethod($call[0], $call[1]);
        }
        catch (ReflectionException | TypeError $prev)
        {
            TypeError::raise(
                    'invalid callable %s',
                    json_encode($call, JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    previous: $prev
            );
        }
    }

    public function __get__(object $obj): mixed
    {

        if ( ! $this->fget)
        {
            return None;
        }
        $callable = $this->get_callable($obj, $this->fget);

        if ($callable instanceof ReflectionMethod)
        {
            $callable->setAccessible(true);
            return $callable->invoke($obj);
        }
        return $callable->invoke();
    }

    public function __set__(object $obj, mixed $value): void
    {

        if ( ! $this->fset)
        {
            return;
        }

        $callable = $this->get_callable($obj, $this->fset);

        if ($callable instanceof ReflectionMethod)
        {
            $callable->setAccessible(true);
            $callable->invoke($obj, $value);
        }
        else
        {
            $callable->invoke($value);
        }
    }

    public function __delete__(object $obj): void
    {

        if ( ! $this->fdel)
        {
            return;
        }

        $callable = $this->get_callable($obj, $this->fdel);

        if ($callable instanceof ReflectionMethod)
        {
            $callable->setAccessible(true);
            $callable->invoke($obj);
        }
        else
        {
            $callable->invoke();
        }
    }

}
