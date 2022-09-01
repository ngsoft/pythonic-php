<?php

declare(strict_types=1);

namespace Python\Collections;

abstract class AbcIterator implements \IteratorAggregate
{

    use AbcIterable;

    /**
     * Return the next item from the iterator.
     * When exhausted, raise StopIteration
     */
    abstract protected function __next__(): mixed;
}
