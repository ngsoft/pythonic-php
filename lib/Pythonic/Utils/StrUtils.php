<?php

declare(strict_types=1);

namespace Pythonic\Utils;

use Pythonic\Errors\ValueError,
    Stringable;

/**
 * Basic Str operations
 */
abstract class StrUtils
{

    /**
     * Get str length
     */
    public static function len(string|Stringable $str): int
    {
        $str = strval($str);
        return mb_strlen($str);
    }

    /**
     * Get char at pos
     */
    public static function charAt(string|Stringable $str, int $pos): string
    {


        if (0 === $len = self::len($str = strval($str)))
        {
            return '';
        }


        if ( ! Utils::in_range($pos, -$len, $len - 1))
        {
            return '';
        }

        return mb_substr($str, $pos, 1);
    }

    /**
     * Get a str slice
     */
    public static function slice(string|Stringable $str, int $startOrStop, int $stop = null, int $step = null): string
    {

        if ($step === 0)
        {
            ValueError::raise('step cannot be 0');
        }

        $len = self::len($str = strval($str));

        $step ??= 1;
        $start = null;

        if ( ! is_int($stop))
        {
            $stop = $startOrStop;
        }
        else
        {
            $start = $startOrStop;
        }

        $stop ??= $step > 0 ? $len : -1;

        $start ??= $step > 0 ? 0 : $len - 1;

        if ($start < 0)
        {
            $start += $len;
        }

        if ($stop < ($step < 0 ? -1 : 0))
        {
            $stop += $len;
        }


        // invalid range
        if ($stop === $start || $step > 0 ? $stop < $start : $stop > $start)
        {
            return '';
        }

        //count steps
        [$min, $max] = $start > $stop ? [$stop, $start] : [$start, $stop];

        $count = intval(ceil(($max - $min) / abs($step)));

        $result = '';

        for ($i = 0; $i < $count; $i ++)
        {
            $offset = $start + ($i * $step);

            // prevent out of range function calls
            if ($offset >= $len)
            {
                if ($step > 0)
                {
                    break;
                }

                continue;
            }
            elseif ($offset < 0)
            {
                if ($step < 0)
                {
                    break;
                }
                continue;
            }

            $result .= mb_substr($str, $offset, 1);
        }

        return $result;
    }

}
