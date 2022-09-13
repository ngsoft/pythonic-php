<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

use Attribute,
    NGSOFT\Pythonic\Utils\Reflection,
    Pythonic\Errors\TypeError,
    ReflectionAttribute;

/**
 * The Infos about an Attribute
 */
class AttributeInfo
{

    protected Attribute $attribute;
    protected int $flags;
    protected bool $repeatable;
    protected array $targets;
    protected ?array $params = null;

    /**
     * get infos for an attribute
     */
    public static function of(string|object $attribute): static
    {
        return new static( ! is_string($attribute) ? get_class($attribute) : $attribute);
    }

    /**
     * get infos for a reflector
     */
    public static function fromReflectionAttribute(ReflectionAttribute $reflector): static
    {
        return static::of($reflector->getName());
    }

    public function __construct(
            public readonly string $name
    )
    {

        if ($attribute = Reader::getClassAttribute($name, Attribute::class))
        {
            $this->attribute = $attribute;
            $flags = $this->flags = $this->attribute->flags;
            $this->repeatable = ($flags & Attribute::IS_REPEATABLE) > 0;
            $this->targets = TargetType::getTargets($flags);
        }
        else
        {
            TypeError::raise('Invalid attribute %s', $name);
        }
    }

    public function isRepeatable(): bool
    {
        return $this->repeatable;
    }

    public function targetParameter(): bool
    {
        return in_array(TargetType::TARGET_PARAMETER, $this->targets);
    }

    public function targetFunction(): bool
    {
        return in_array(TargetType::TARGET_FUNCTION, $this->targets);
    }

    public function targetClass(): bool
    {
        return in_array(TargetType::TARGET_CLASS, $this->targets);
    }

    public function targetClassConstant(): bool
    {
        return in_array(TargetType::TARGET_CLASS_CONSTANT, $this->targets);
    }

    public function targetMethod(): bool
    {
        return in_array(TargetType::TARGET_METHOD, $this->targets);
    }

    public function targetProperty(): bool
    {
        return in_array(TargetType::TARGET_PROPERTY, $this->targets);
    }

    /**
     * Get attribute parameters
     * @return AttributeParameter[]
     */
    public function getParams()
    {

        if (null === $this->params)
        {

            $this->params = [];

            $reflectClass = Reflection::getClass($this->name);

            if ($construct = $reflectClass->getConstructor())
            {
                foreach ($construct->getParameters() as $param)
                {
                    $instance = AttributeParameter::of($param);
                    $this->params[$instance->name] = $instance;
                }
            }
        }


        return $this->params;
    }

}
