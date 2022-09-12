<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

/**
 * Attribute Target Information
 */
class Target
{

    /**
     * Get an instance parsing ReflectionAttribute Informations
     */
    public static function of(\ReflectionAttribute $reflector): static
    {
        return new static($reflector->getName(), $reflector->getTarget());
    }

    public function __construct(
            public readonly string $name,
            public readonly int $target
    )
    {

    }

}
