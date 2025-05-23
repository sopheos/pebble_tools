<?php

namespace Pebble\Tools;

class Bitwise
{
    public static function has(int $value, int $key): bool
    {
        return $value & $key;
    }

    public static function add(int &$value, int $key): int
    {
        if (!self::has($value, $key)) {
            return $value + $key;
        }
        return $value;
    }

    public static function del(int &$value, int $key): int
    {
        if (self::has($value, $key)) {
            return $value - $key;
        }
        return $value;
    }
}
