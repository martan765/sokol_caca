<?php

namespace App\View;

use DateTimeInterface;
use Illuminate\Support\Carbon;

final class Dates
{
    public const LOCALE = 'cs';

    public static function translated(DateTimeInterface $date, string $format): string
    {
        return Carbon::instance($date)->locale(self::LOCALE)->translatedFormat($format);
    }

    public static function daysUntil(DateTimeInterface $date): int
    {
        return (int) Carbon::now()->startOfDay()->diffInDays(Carbon::instance($date)->startOfDay());
    }
}
