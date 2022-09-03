<?php

declare(strict_types=1);

namespace Pythonic\Utils;

use Pythonic\{
    Errors\ImportError, Traits\NotInstanciable, Traits\Singleton
};
use ReflectionClass,
    ReflectionException,
    ReflectionFunction;

class Importer
{

    use NotInstanciable,
        Singleton;

    protected static $_aliases = [];

    /**
     * Mapped previous results
     * @var array<string, string>
     */
    protected static array $_cache = [];

    /**
     * Prefix to import
     * @var string|null
     */
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

        if ($from === $to)
        {
            return;
        }

        static::$_aliases[self::convertToPythonic($to)] = self::convertToPythonic($from);
    }

    protected static function getAlias(string $namespace): string
    {
        return isset(static::$_aliases[$namespace]) ? static::getAlias(static::$_aliases[$namespace]) : $namespace;
    }

    /**
     * Replaces the python from keyword
     * usage $resource = from('namespace.subnamespace')->import('name')
     */
    public static function from(string $namespace): static
    {
        $self = static::instance();

        $self->from = $namespace === '' ? null : $namespace;

        return $self;
    }

    /**
     * imports resource(s) that can be a function name or class name
     */
    public static function import(string|array $resource, &$as = null, ?string $from = null): string|array
    {

        try
        {
            if ($from)
            {
                static::from($from);
            }


            return
                    $as = is_array($resource) ?
                    static::instance()->importMany($resource) :
                    static::instance()->importSingle($resource);
        } finally
        {
            static::instance()->from = null;
        }
    }

    /**
     * Returse a tuple of resources
     */
    protected function importMany(array $resources): array
    {

        if (empty($resources))
        {
            ImportError::raise();
        }


        $result = [];

        while (null !== $resource = array_shift($resources))
        {
            $result[] = $this->importSingle($resource);
        }

        return $result;
    }

    /**
     * imports a resource that can be a function name or class name
     */
    protected function importSingle(string $resource): string
    {


        try
        {
            if ($this->from)
            {
                $resource = $this->from . ".$resource";
            }

            if (isset(static::$_cache[$resource]))
            {
                return static::$_cache[$resource];
            }

            /**
             * loads pythonic modules without using 'pythonic' namespace
             */
            foreach (['', 'pythonic.'] as $prefix)
            {
                $php = static::convertToPhp(static::getAlias($prefix . $resource));

                if (function_exists($php))
                {
                    // get the cased name and caches the result
                    return static::$_cache[$resource] = (new ReflectionFunction($php))->getName();
                } elseif (class_exists($php))
                {
                    // get the cased name and caches the result
                    return static::$_cache[$resource] = (new ReflectionClass($php))->getName();
                }
            }
        } catch (ReflectionException)
        {
            // do nothing
        }

        return ImportError::raise('Cannot find module: %s', $resource);
    }

}
