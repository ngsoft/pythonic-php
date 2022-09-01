<?php

declare(strict_types=1);

namespace Python\Collections;

use Countable,
    IteratorAggregate,
    JsonSerializable,
    Python\PClass,
    Throwable;

abstract class AbcCollection extends PClass implements \ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{

    use AbcSized,
        AbcIterable,
        AbcContainer;

    protected function __repr__(): string
    {
        return $this->toJson();
    }

    /**
     * Exports Collection to array
     */
    public function toArray(): array
    {

        $array = [];

        foreach ($this as $offset => $value)
        {
            if ($value instanceof self)
            {
                $value = $value->toArray();
            }
            $array [$offset] = $value;
        }

        return $array;
    }

    /**
     * Exports pCollection to json
     */
    public function toJson(int $flags = JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    /**
     * Return a shallow copy of the collection
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Checks if a value is a collection with the same items as current
     */
    public function equals(mixed $value): bool
    {

        if ($value instanceof self)
        {
            $value = $value->toArray();
        }

        if (is_array($value))
        {
            return $value === $this->toArray();
        }

        return false;
    }

    /**
     * Override this for better performances
     */
    public function offsetExists(mixed $offset): bool
    {

        try
        {
            return $this[$offset] !== null;
        } catch (Throwable)
        {
            return false;
        }
    }

    /**
     * Export Collection to json
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function __debugInfo(): array
    {
        return $this->toArray();
    }

}
