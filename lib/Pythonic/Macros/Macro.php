<?php

declare(strict_types=1);

namespace Pythonic\Macros;

use Closure,
    Pythonic\Errors\TypeError,
    ReflectionClass,
    ReflectionException,
    ReflectionMethod;
use function Pythonic\isinstance;

class Macro
{

    /**
     * Creates a macro from a callable
     */
    public static function fromCallable(callable $callable, string $name, bool $isStatic = false): static
    {
        if ( ! isinstance($callable, Closure::class))
        {
            $callable = Closure::fromCallable($callable);
        }

        return new static($name, $callable, $isStatic);
    }

    /**
     * Creates multiple instances from an object
     */
    public static function fromObject(object $object): array
    {


        $class = get_class($object);
        $result = [];

        try
        {

            $methods = (new ReflectionClass($class))->getMethods(
                    ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
            );

            /** @var ReflectionMethod $method */
            foreach ($methods as $method)
            {
                if ($method->isConstructor() || $method->isDestructor() || $method->isStatic())
                {
                    continue;
                }

                $name = $method->getName();

                if ($closure = $method->getClosure($method->isStatic() ? null : $object))
                {
                    $result[$name] ??= static::fromCallable($closure, $name, $method->isStatic());
                }
            }
        }
        catch (ReflectionException $prev)
        {
            TypeError::raise('Invalid class %s', $class, previous: $prev);
        }

        return $result;
    }

    /**
     * Creates multiple instances from a class (static methods)
     */
    public static function fromStatic(string|object $class): array
    {

        $result = [];

        if (is_object($class))
        {
            $class = get_class($class);
        }


        try
        {

            $methods = (new ReflectionClass($class))->getMethods(
                    ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_STATIC
            );

            /** @var ReflectionMethod $method */
            foreach ($methods as $method)
            {

                $name = $method->getName();

                if ($closure = $method->getClosure())
                {
                    $result[$name] ??= static::fromCallable($closure, $name, true);
                }
            }
        }
        catch (\ReflectionException $prev)
        {
            TypeError::raise('Invalid class %s', $class, previous: $prev);
        }

        return $result;
    }

    public function __construct(
            protected string $name,
            protected Closure $callable,
            protected bool $isStatic = false
    )
    {

    }

    public function __invoke(object|string $self, array $arguments = []): mixed
    {

        $class = $self;

        if ( ! is_string($class))
        {
            $class = get_class($class);
        }
        else
        {
            $self = null;
        }


        $callable = $this->callable->bindTo($self, $class);
        return call_user_func_array($callable, $arguments);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }

}
