<?php

namespace Sokol\Application\Support;

use DateTimeInterface;

final class Changes
{
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    public static function between(array $before, array $after): array
    {
        $changes = [];

        foreach ($after as $field => $value) {
            $value = self::normalize($value);

            if (! array_key_exists($field, $before) || self::normalize($before[$field]) !== $value) {
                $changes[$field] = $value;
            }
        }

        return $changes;
    }

    private static function normalize(mixed $value): mixed
    {
        return $value instanceof DateTimeInterface ? $value->format(self::DATE_FORMAT) : $value;
    }
}
