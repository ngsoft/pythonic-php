<?php

declare(strict_types=1);

namespace Python\Collections;

/**
 * A Mapping is a generic container for associating key/value pairs.
 */
abstract class AbcMapping extends AbcCollection
{

    abstract protected function __getitem__(mixed $key): mixed;

    public function get(mixed $key, mixed $default = null): mixed
    {

        try
        {
            return $this[$key];
        } catch (\Python\KeyError)
        {
            return $default;
        }
    }

    protected function __contains__(mixed $value): bool
    {

        try
        {
            return $this[$value] !== null;
        } catch (\Python\KeyError)
        {
            return false;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->__contains__($offset);
    }

}
