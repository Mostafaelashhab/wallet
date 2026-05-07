<?php

namespace App\Services;

use InvalidArgumentException;

class SplitCalculator
{
    /**
     * @param  array<int,float|int|null>  $shares  user_id => share value (weight, percent, or exact)
     * @return array<int,float>  user_id => amount
     */
    public static function compute(string $type, float $total, array $shares): array
    {
        $shares = array_filter($shares, fn ($v) => $v !== null);
        if (empty($shares)) {
            throw new InvalidArgumentException('At least one share is required');
        }

        return match ($type) {
            'equal' => self::equal($total, array_keys($shares)),
            'exact' => self::exact($total, $shares),
            'percent' => self::percent($total, $shares),
            'shares' => self::shares($total, $shares),
            default => throw new InvalidArgumentException("Unknown split type: $type"),
        };
    }

    private static function equal(float $total, array $userIds): array
    {
        $count = count($userIds);
        $base = floor(($total * 100) / $count) / 100;
        $totalCents = (int) round($total * 100);
        $baseCents = (int) round($base * 100);
        $remainder = $totalCents - ($baseCents * $count);
        $out = [];
        foreach ($userIds as $i => $uid) {
            $cents = $baseCents + ($i < $remainder ? 1 : 0);
            $out[$uid] = $cents / 100;
        }
        return $out;
    }

    private static function exact(float $total, array $shares): array
    {
        $sum = array_sum($shares);
        if (abs($sum - $total) > 0.01) {
            throw new InvalidArgumentException("Exact splits ({$sum}) must equal total ({$total})");
        }
        return array_map(fn ($v) => round((float) $v, 2), $shares);
    }

    private static function percent(float $total, array $shares): array
    {
        $sum = array_sum($shares);
        if (abs($sum - 100) > 0.1) {
            throw new InvalidArgumentException("Percentages must sum to 100, got {$sum}");
        }
        $out = [];
        $assigned = 0;
        $keys = array_keys($shares);
        $last = end($keys);
        foreach ($shares as $uid => $pct) {
            if ($uid === $last) {
                $out[$uid] = round($total - $assigned, 2);
            } else {
                $amt = round($total * ($pct / 100), 2);
                $out[$uid] = $amt;
                $assigned += $amt;
            }
        }
        return $out;
    }

    private static function shares(float $total, array $shares): array
    {
        $sum = array_sum($shares);
        if ($sum <= 0) throw new InvalidArgumentException('Shares must sum > 0');
        $out = [];
        $assigned = 0;
        $keys = array_keys($shares);
        $last = end($keys);
        foreach ($shares as $uid => $w) {
            if ($uid === $last) {
                $out[$uid] = round($total - $assigned, 2);
            } else {
                $amt = round($total * ($w / $sum), 2);
                $out[$uid] = $amt;
                $assigned += $amt;
            }
        }
        return $out;
    }
}
