<?php

declare(strict_types=1);

namespace Python\Collections;

class AbcSet extends AbcCollection
{

    protected function __le__(mixed $other): bool
    {


        if (len($this) > len($other))
        {
            return false;
        }

        foreach ($this as $elem)
        {
            if ( ! $other->__contains__($elem))
            {
                return false;
            }
        }

        return true;
    }

    protected function __ge__(mixed $other): bool
    {

        if (len($this) < len($other))
        {
            return false;
        }


        foreach ($other as $elem)
        {
            if ( ! $this->__contains__($elem))
            {
                return false;
            }
        }

        return true;
    }

    protected function __lt__(mixed $other): bool
    {
        return len($this) < len($other) && $this->__le__($other);
    }

    protected function __gt__(mixed $other): bool
    {
        return len($this) > len($other) && $this->__ge__($other);
    }

    protected function __eq__(mixed $other): bool
    {
        return len($this) === len($other) && $this->__le__($other);
    }

}
