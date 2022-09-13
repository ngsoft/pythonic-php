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
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Property extends BaseAttribute
{

    protected string|null $fget = null;
    protected string|null $fset = null;
    protected string|null $fdel = null;
    protected ?Closure $getter = null;
    protected ?Closure $setter = null;
    protected ?Closure $deleter = null;

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

            if ( ! $attr->fget)
            {
                $attr->getter($attr->getName());
            }

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
            string|Closure|null $fget = null,
            string|Closure|null $fset = null,
            string|Closure|null $fdel = null,
            protected ?string $name = null
    )
    {
        // setting as closures to prevent infinite loops for properties named the same as methods
        $fget && $this->getter($fget);
        $fset && $this->setter($fset);
        $fdel && $this->deleter($fdel);
    }

    public function getName(): string
    {
        return $this->name ??= $this->container->getName();
    }

    public function getter(string|Closure $fget): static
    {

        $this->fget = null;
        if (is_string($fget))
        {
            $this->fget = $fget;
            $fget = function () use ($fget)
            {
                if ( ! method_exists($this, $fget))
                {
                    TypeError::raise('object %s method %s is not accessible.', get_class($this), $fget);
                }

                return $this->{$fget}();
            };
        }


        $this->getter = $fget;
        return $this;
    }

    public function setter(string|Closure $fset): static
    {


        $this->fset = null;
        if (is_string($fset))
        {

            $this->fset = $fset;

            $fset = function (mixed $value) use ($fset)
            {
                if ( ! method_exists($this, $fset))
                {
                    TypeError::raise('object %s method %s is not accessible.', get_class($this), $fset);
                }

                $this->{$fset}($value);
            };
        }


        $this->setter = $fset;
        return $this;
    }

    public function deleter(string|Closure $fdel): static
    {

        $this->fdel = null;

        if (is_string($fdel))
        {
            $this->fdel = $fdel;

            $fdel = function () use ($fdel)
            {

                if ( ! method_exists($this, $fdel))
                {
                    TypeError::raise('object %s method %s is not accessible.', get_class($this), $fdel);
                }

                $this->{$fdel}();
            };
        }

        $this->deleter = $fdel;
        return $this;
    }

    /**
     * Binds Closure to obj if possible
     */
    protected function getCallable(object $obj, Closure $method): callable
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

    public function __get__(object $obj): mixed
    {

        if ( ! $this->getter)
        {
            AttributeError::raiseForAttribute('can\'t get attribute');
        }

        return call_user_func($this->getCallable($obj, $this->getter));
    }

    public function __set__(object $obj, mixed $value): void
    {

        if ( ! $this->setter)
        {
            AttributeError::raiseForAttribute('can\'t set attribute');
        }


        call_user_func($this->getCallable($obj, $this->setter), $value);
    }

    public function __delete__(object $obj): void
    {

        if ( ! $this->deleter)
        {
            AttributeError::raiseForAttribute('can\'t delete attribute');
        }

        call_user_func($this->getCallable($obj, $this->deleter));
    }

    public function __serialize(): array
    {

        return [
            $this->name,
            $this->fget,
            $this->fset,
            $this->fdel,
            parent::__serialize()
        ];
    }

    public function __unserialize(array $data): void
    {
        [$this->name, $fget, $fset, $fdel, $parent] = $data;

        $fget && $this->getter($fget);
        $fset && $this->setter($fset);
        $fdel && $this->deleter($fdel);
        parent::__unserialize($parent);
    }

}
