<?php

declare(strict_types=1);

namespace Pythonic\Utils;

use Pythonic\Errors\ImportError,
    ReflectionException;

class Importer
{

    protected static $_aliases = [];
    protected ?string $from = null;

    protected static function convertToPythonic(string $namespace): string
    {
        $namespace = trim($namespace, '.\\');

        $namespace = str_replace('\\', '.', $namespace);

        return mb_strtolower(preg_replace('#\.+#', '.', $namespace));
    }

    protected static function convertToPhp(string $namespace): string
    {
        $namespace = trim($namespace, '.\\');
        $namespace = preg_replace('#\.+#', '.', $namespace);

        return str_replace('.', '\\', $namespace);
    }

    /**
     * Alias a namespace
     */
    public static function alias(string $to, string $from): void
    {
        static::$_aliases[self::convertToPythonic($to)] = self::convertToPythonic($from);
    }

    protected function getAlias(string $namespace): string
    {
        return isset(static::$_aliases[$namespace]) ? $this->getAlias(static::$_aliases[$namespace]) : $namespace;
    }

    /**
     * Replaces the ptyhon from keyword
     * usage $resource = from('namespace.subnamespace')->import('name')
     *
     */
    public function from(string $namespace): static
    {
        $this->from = $this->getAlias($namespace);
        return $this;
    }

    public function import(string $resource): mixed
    {


        try
        {
            if ($this->from)
            {
                $resource = $this->from . ".$resource";
            }

            $php = static::convertToPhp($resource);

            if (function_exists($php))
            {
                return (new \ReflectionFunction($php))->getName();
            } elseif (class_exists($resource))
            {
                return (new \ReflectionClass($php))->getName();
            }
        } catch (ReflectionException)
        {
            // do nothing
        } finally
        {
            $this->from = null;
        }

        return ImportError::raise('Cannot find module: %s', $resource);
    }

}
