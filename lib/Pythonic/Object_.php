<?php

declare(strict_types=1);

namespace Pythonic;

/**
 * The base python object
 * All Pythonic classes extends this one
 *
 * Object is a PHP reserved keyword
 */
class Object_
{

    protected ?string $__doc__ = None;
    protected array|\ArrayAccess $__dict__ = [];
    protected string $__module__ = '';

    public function __construct()
    {

        if (static::class === __CLASS__)
        {
            return;
        }
    }

}
