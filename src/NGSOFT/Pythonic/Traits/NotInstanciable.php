<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Traits;

/**
 * Prevents non abstract class to be instanciated outside of itself
 */
trait NotInstanciable
{

    protected function __construct()
    {

    }

}
