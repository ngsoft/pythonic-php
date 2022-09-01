<?php

declare(strict_types=1);

namespace Python\Collections;

use Countable,
    IteratorAggregate,
    Python\PClass;

abstract class AbcCollection extends PClass implements Countable, IteratorAggregate
{

    use AbcSized,
        AbcIterable,
        AbcContainer;
}
