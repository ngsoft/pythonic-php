<?php

declare(strict_types=1);

use Pythonic\Traits\Singleton;

$__all__ = [];

require_once __DIR__ . '/Pythonic/builtin.php';

var_dump((new ReflectionClass(\pythonic\builtinfunctions::class))->getName());

var_dump((new ReflectionFunction('pythonic\\t'))->getName());
