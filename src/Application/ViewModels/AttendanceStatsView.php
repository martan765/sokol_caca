<?php

namespace Sokol\Application\ViewModels;

final readonly class AttendanceStatsView
{
    public function __construct(
        public int $total,
        public int $attended,
        public int $percentage,
    ) {}

    public static function of(int $total, int $attended): self
    {
        return new self($total, $attended, $total > 0 ? (int) round($attended / $total * 100) : 0);
    }
}
