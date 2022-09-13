<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

use ReflectionAttribute,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty;

/**
 * A Base Class to use for Attributes
 */
abstract class BaseAttribute
{

    protected Target $target;
    protected ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionParameter|ReflectionProperty|ReflectionClassConstant $container;

    public function getTarget(): Target
    {
        return $this->target;
    }

    public function getContainer(): ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionParameter|ReflectionProperty|ReflectionClassConstant
    {
        return $this->container;
    }

    public static function fromReflectionAttribute(
            ReflectionAttribute $reflector,
            ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionParameter|ReflectionProperty|ReflectionClassConstant $container
    ): object
    {

        $instance = $reflector->newInstance();
        if ($instance instanceof self)
        {
            $instance->target = Target::fromReflectionAttribute($reflector);
            $instance->container = $container;
        }
        return $instance;
    }

    /**
     * @phan-suppress PhanRedundantCondition
     */
    public function __serialize(): array
    {

        $container = [
            get_class($this->container)
        ];

        if (isset($this->container->class))
        {
            $container[] = $this->container->class;
        }
        $container[] = $this->container->name;

        return [$container, $this->target];
    }

    public function __unserialize(array $data): void
    {
        [$container, $this->target] = $data;

        if ($class = array_shift($container))
        {
            $this->container = new $class(...$container);
        }
    }

}
