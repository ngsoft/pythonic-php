<?php

declare(strict_types=1);

namespace Pythonic\Traits;

/**
 * Prevents class to be instanciated outside of itself
 */
trait NotInstanciable
{

    protected function __construct()
    {

    }

}
