<?php

declare(strict_types=1);

namespace Python\Collections;

/**
 * A Mapping is a generic container for associating key/value pairs.
 */
abstract class AbcMapping extends AbcCollection
{

    protected function __getitem__(mixed $key): mixed
    {

    }

}
