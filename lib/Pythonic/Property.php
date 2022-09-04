<?php

declare(strict_types=1);

namespace Pythonic;

use Attribute,
    Closure,
    Pythonic\Errors\TypeError,
    ReflectionException,
    ReflectionFunction,
    ReflectionMethod;
use const None;
use function array_is_list;

/**
 * The python property
 * use this as attribute to retain "@property"
 * this can also be uses inside your constructor for protected or lower properties
 * eg: $this->prop = new Property('getProp', 'setProp')
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Property
{

    protected $fget = None;
    protected $fset = None;
    protected $fdel = None;

    public function __construct(
            $fget = None,
            $fset = None,
            $fdel = None,
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
     * @phan-suppress PhanPluginAlwaysReturnMethod
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

    public function __get__(object|string $obj): mixed
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

    public function __set__(object|string $obj, mixed $value): void
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

    public function __delete__(object|string $obj): void
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
