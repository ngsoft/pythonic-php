<?php

declare(strict_types=1);

use Pythonic\Typing\Types;

if ( ! defined('NAMESPACE_SEPARATOR'))
{
    define('NAMESPACE_SEPARATOR', '\\');
}


if ( ! defined('None'))
{
    /**
     * Python None
     */
    define('None', null);
}



require_once __DIR__ . '/Pythonic/Builtin.php';

Types::__boot__();

