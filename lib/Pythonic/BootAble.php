<?php

declare(strict_types=1);

namespace Pythonic;

interface BootAble
{

    public static function __boot__(): void;

    public static function __all__(object $self): array;
}
