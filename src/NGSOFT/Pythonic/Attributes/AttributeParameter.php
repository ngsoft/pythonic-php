<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

class AttributeParameter
{

    /**
     * Create from ReflectionParamater data
     */
    public static function of(\ReflectionParameter $reflector): static
    {
        return new static($reflector->getName(), strval($reflector->getType() ?? 'mixed'), $reflector->isVariadic());
    }

    public function __construct(
            public readonly string $name,
            public readonly string $type,
            public readonly bool $isVariadic = false
    )
    {

    }

}
