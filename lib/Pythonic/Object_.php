<?php

declare(strict_types=1);

namespace Pythonic;

use ArrayAccess;
use const None;

/**
 * The base python object
 * All Pythonic classes extends this one
 *
 * Object is a PHP reserved keyword
 */
class Object_
{

    protected array|ArrayAccess $__dict__ = [];

    #[Property('getProp')]
    protected $prop = 10;

    public function __construct()
    {

        if (static::class === __CLASS__)
        {
            //return;
        }

        /** @var Property $instance */
        foreach (Property::of($this) as $prop => $instance)
        {
            // add dynamic properties
            if ($instance->isAttribute)
            {
                $this->__dict__[$prop] = $instance;
                continue;
            }

            // instanciate properties if not already
            if ( ! isset($this->{$prop}))
            {
                $this->{$prop} = $instance;
            }
        }
    }

}
