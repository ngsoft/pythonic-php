<?php

declare(strict_types=1);

namespace NGSOFT\Pythonic\Attributes;

use Attribute;

/**
 * The different target types
 */
enum TargetType: int
{

    case TARGET_CLASS = Attribute::TARGET_CLASS;
    case TARGET_FUNCTION = Attribute::TARGET_FUNCTION;
    case TARGET_METHOD = Attribute::TARGET_METHOD;
    case TARGET_PROPERTY = Attribute::TARGET_PROPERTY;
    case TARGET_CLASS_CONSTANT = Attribute::TARGET_CLASS_CONSTANT;
    case TARGET_PARAMETER = Attribute::TARGET_PARAMETER;

    /**
     * Get target from bitmask
     */
    public static function getTargets(int $flags): array
    {
        return array_filter(static::cases(), fn($enum) => ($flags & $enum->value) > 0);
    }

}
