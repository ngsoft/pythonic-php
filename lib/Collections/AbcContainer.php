<?php

declare(strict_types=1);

namespace Python\Collections;

trait AbcContainer
{

    /**
     * Checks if object contains value
     */
    abstract protected function __contains__(mixed $value): bool;
}
