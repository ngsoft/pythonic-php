<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

use ReflectionAttribute,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionFunction,
    ReflectionFunctionAbstract,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty;

/**
 * A Base Class to use for Attributes
 */
class BaseAttribute
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

        $name = $container->getName();

        $instance = $reflector->newInstance();
        if ($instance instanceof self)
        {
            $instance->target = Target::fromReflectionAttribute($reflector);
            $instance->container = $container;
        }
        return $instance;
    }

}
