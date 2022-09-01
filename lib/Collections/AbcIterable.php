<?php

declare(strict_types=1);

namespace Python\Collections;

/**
 * Class using this trait must implement IteratorAgregate
 */
trait AbcIterable
{

    /**
     * Returns an iterator
     */
    abstract protected function __iter__(): iterable;

    public function getIterator(): \Traversable
    {
        yield from $this->__iter__();
    }

}
