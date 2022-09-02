<?php

declare(strict_types=1);

namespace Pythonic\Types;

interface BootAble
{

    public static function __boot__(): void;
}
