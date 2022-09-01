<?php

declare(strict_types=1);

namespace Python\Collections;

/**
 * Class using this trait must implement Countable
 */
trait AbcSized
{

    /**
     * Returns the object length
     */
    abstract protected function __len__(): int;

    public function count(): int
    {
        return $this->__len__();
    }

}
