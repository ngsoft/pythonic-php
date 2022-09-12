<?php

declare(strict_types=1);

namespace Pythonic;

use Attribute,
    Closure,
    ErrorException;
use NGSOFT\Pythonic\{
    Attributes\BaseAttribute, Attributes\Reader, Utils\Utils
};
use Pythonic\Errors\{
    AttributeError, TypeError
};

/**
 * The python property
 * use this as attribute to retain "@property"
 * this can also be used inside your constructor for protected or lower properties
 * eg: $this->prop = new Property('getProp', 'setProp')
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Property extends BaseAttribute
{

    /**
     * Scan for all attributes and return type for class and returns instances
     */
    public static function of(object $class): array
    {


        $instances = [];

        /** @var self $attr */
        foreach (Reader::getPropertiesAttributes($class, __CLASS__) as $attr)
        {
            $instances[$attr->getName()] ??= $attr;
        }

        foreach (Reader::getMethodsAttributes($class, __CLASS__) as $attr)
        {

            $attr->fget ??= $attr->getName();
            $instances[$attr->getName()] ??= $attr;
        }


        return $instances;
    }

    /**
     * @param string|null $fget Getter method name
     * @param string|null $fset Setter method name
     * @param string|null $fdel Deleter method name
     * @param string|null $name property name
     */
    public function __construct(
            protected string|Closure|null $fget = null,
            protected string|Closure|null $fset = null,
            protected string|Closure|null $fdel = null,
            protected ?string $name = null
    )
    {

    }

    public function getName(): string
    {
        return $this->name ??= $this->container->getName();
    }

    public function getter(string|Closure $fget): static
    {
        $this->fget = $fget;
        return $this;
    }

    public function setter(string|Closure $fset): static
    {
        $this->fset = $fset;
        return $this;
    }

    public function deleter(string|Closure $fdel): static
    {
        $this->fdel = $fdel;
        return $this;
    }

    protected function getCallable(object $obj, string|Closure $method): callable
    {

        if ($method instanceof Closure)
        {
            try
            {
                Utils::errors_as_exception();
                return $method->bindTo($obj, get_class($obj));
            }
            catch (ErrorException)
            {
                return $method;
            }
            finally
            {
                restore_error_handler();
            }
        }

        if ( ! method_exists($obj, $method))
        {
            TypeError::raise('object %s method %s is not accessible.', get_class($obj), $method);
        }

        return [$obj, $method];
    }

    public function __get__(object $obj): mixed
    {

        if ( ! $this->fget)
        {
            AttributeError::of('can\'t get attribute');
        }

        return call_user_func($this->getCallable($obj, $this->fget));
    }

    public function __set__(object $obj, mixed $value): void
    {

        if ( ! $this->fset)
        {
            AttributeError::of('can\'t set attribute');
        }

        call_user_func($this->getCallable($obj, $this->fset), $value);
    }

    public function __delete__(object $obj): void
    {

        if ( ! $this->fdel)
        {
            AttributeError::of('can\'t delete attribute');
        }

        call_user_func($this->getCallable($obj, $this->fdel));
    }

}
