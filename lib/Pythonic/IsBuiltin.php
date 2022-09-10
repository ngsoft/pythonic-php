<?php

declare(strict_types=1);

namespace Pythonic;

/**
 * Defines methods as builtin
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class IsBuiltin
{

    public function __construct(public readonly string $class)
    {

    }

}
