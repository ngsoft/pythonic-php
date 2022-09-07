<?php

declare(strict_types=1);

namespace Pythonic;

use Attribute;
use Pythonic\{
    Errors\TypeError, Utils\AttributeReader, Utils\Reflection
};
use const None;

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
    public static function of(object $class): array
    {


        $instances = [];

        /** @var self $attr */
        foreach (AttributeReader::getPropertyAttributes($class, __CLASS__) as $prop => $attr)
        {
            $attr->setName($name = $attr->getName() ?? $prop);
            $instances[$name] ??= $attr;
        }

        foreach (AttributeReader::getMethodAttributes($class, __CLASS__) as $method => $attr)
        {

            $attr->setName($name = $attr->getName() ?? $method);
            $instances[$name] ??= $attr;
        }

        // reads properties return values and initialize those with exact return type

        /** @var \ReflectionProperty $reflectionProperty */
        foreach (Reflection::getProperties($class) as $reflectionProperty)
        {

            if (
                    $reflectionProperty->hasType() &&
                    ! $reflectionProperty->hasDefaultValue() &&
                    (string) $reflectionProperty->getType() === __CLASS__ &&
                    ! $reflectionProperty->isInitialized($class)
            )
            {
                $name = $reflectionProperty->getName();
                $instance = new static(name: $name);
                $instances[$name] ??= $instance;
                $reflectionProperty->setValue($class, $instance);
            }
        }




        return $instances;
    }

    /**
     * @param string|null $fget Getter method name
     * @param string|null $fset Setter method name
     * @param string|null $fdel Deleter method name
     * @param string|null $name Method name
     */
    public function __construct(
            ?string $fget = None,
            ?string $fset = None,
            ?string $fdel = None,
            ?string $name = None
    )
    {

        $this->name = $name;

        $fget && $this->getter($fget);
        $fset && $this->setter($fset);
        $fdel && $this->deleter($fdel);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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

        call_user_func($this->getCallable($obj, $this->fset), $value);
    }

    public function __delete__(object $obj): void
    {

        if ( ! $this->fdel)
        {
            return;
        }

        call_user_func($this->getCallable($obj, $this->fdel));
    }

}
