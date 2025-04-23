<?php

namespace Pebble\Tools;

use JsonException;

class A
{
    public static function parse(mixed $value): array
    {
        if (is_string($value)) {
            try {
                $value = json_decode($value, true, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return [];
            }
        }

        if (is_array($value) || is_object($value)) {
            return json_decode(json_encode($value), true);
        }

        return [];
    }

    public static function equal(array $a, array $b): bool
    {
        return count($a) === count($b) && !array_diff($a, $b);
    }

    public static function rand(array $data)
    {
        if (!$data) {
            return null;
        }

        $key = array_rand($data, 1);
        return $data[$key];
    }

    public static function unset(array $values, string $key): array
    {
        $values[$key] = null;
        unset($values[$key]);

        return $values;
    }

    public static function unique(array $rows, string|int|null $column_key = null): array
    {
        if ($column_key) {
            $rows = array_column($rows, $column_key);
        }

        return array_values(array_filter(array_unique($rows), function ($i) {
            return $i !== null && $i !== '' && $i !== [];
        }));
    }
}
