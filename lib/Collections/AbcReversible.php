<?php

declare(strict_types=1);

namespace Python\Collections;

/**
 * Class using this trait must implement IteratorAgregate
 */
trait AbcReversible
{

    use AbcIterable;

    /**
     * Returns a reversed iterator
     */
    abstract protected function __reversed__(): iterable;
}
