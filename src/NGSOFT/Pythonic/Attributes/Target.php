<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

/**
 * Attribute Target Information
 */
class Target implements \Stringable
{

    protected TargetType $type;

    /**
     * Get an instance parsing ReflectionAttribute Informations
     */
    public static function fromReflectionAttribute(\ReflectionAttribute $reflector): static
    {
        return new static($reflector->getTarget());
    }

    public function __construct(
            public readonly int $target
    )
    {
        $this->type = TargetType::from($target);
    }

    public function __toString(): string
    {
        return $this->type->name;
    }

}
