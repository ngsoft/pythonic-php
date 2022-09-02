<?php

declare(strict_types=1);

namespace Pythonic\Utils;

class Importer
{

    protected static $_namespaces = [];

    public static function __register__(string|array $namespace, string|array $from = []): void
    {

        if (empty($namespace))
        {

        }


        $namespace = (array) $namespace;
        $from = (array) $from;
    }

    public function register(string|array $namespace, string|array $from = []): void
    {
        self::__register__($namespace, $from);
    }

}
