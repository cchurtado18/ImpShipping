<?php

namespace App\Services;

class PricingService
{
    /**
     * Calculate box price based on cubic feet using the new pricing structure
     *
     * @param float $cubicFeet
     * @param array|null $weightData For small boxes (0.1 - 2.99 ft³)
     * @return float
     */
    public static function calculateBoxPrice(float $cubicFeet, ?array $weightData = null): float
    {
        // Si es caja pequeña, usar cálculo por peso
        if ($cubicFeet >= 0.1 && $cubicFeet <= 2.99 && $weightData) {
            return $weightData['lbs'] * $weightData['rate'];
        }

        // Usar la nueva fórmula volumétrica
        if ($cubicFeet >= 2.90 && $cubicFeet <= 3.89) {
            return round($cubicFeet * 55.52);
        } elseif ($cubicFeet >= 3.90 && $cubicFeet <= 4.89) {
            return round($cubicFeet * 51.52);
        } elseif ($cubicFeet >= 4.90 && $cubicFeet <= 5.89) {
            return round($cubicFeet * 49.02);
        } elseif ($cubicFeet >= 5.90 && $cubicFeet <= 6.89) {
            return round($cubicFeet * 45.52);
        } elseif ($cubicFeet >= 6.90 && $cubicFeet <= 7.89) {
            return round($cubicFeet * 41.52);
        } elseif ($cubicFeet >= 7.90 && $cubicFeet <= 8.89) {
            return round($cubicFeet * 35.75);
        } elseif ($cubicFeet >= 8.90 && $cubicFeet <= 9.89) {
            return round($cubicFeet * 34.75);
        } elseif ($cubicFeet >= 9.90 && $cubicFeet <= 10.89) {
            return round($cubicFeet * 33.25);
        } elseif ($cubicFeet >= 10.90 && $cubicFeet <= 11.89) {
            return round($cubicFeet * 32.75);
        } elseif ($cubicFeet >= 11.90 && $cubicFeet <= 12.89) {
            return round($cubicFeet * 31.75);
        } elseif ($cubicFeet >= 12.90 && $cubicFeet <= 13.89) {
            return round($cubicFeet * 30.25);
        } elseif ($cubicFeet >= 13.90 && $cubicFeet <= 14.89) {
            return round($cubicFeet * 29.25);
        } elseif ($cubicFeet >= 14.90 && $cubicFeet <= 16.99) {
            return round($cubicFeet * 28.25);
        } elseif ($cubicFeet >= 17 && $cubicFeet <= 19.99) {
            return round($cubicFeet * 27.75);
        } elseif ($cubicFeet >= 20) {
            return round($cubicFeet * 25.75);
        }

        return 0;
    }

    /**
     * Get pricing tiers for display purposes
     *
     * @return array
     */
    public static function getPricingTiers(): array
    {
        return [
            ['min' => 2.90, 'max' => 3.89, 'rate' => 55.52],
            ['min' => 3.90, 'max' => 4.89, 'rate' => 51.52],
            ['min' => 4.90, 'max' => 5.89, 'rate' => 49.02],
            ['min' => 5.90, 'max' => 6.89, 'rate' => 45.52],
            ['min' => 6.90, 'max' => 7.89, 'rate' => 41.52],
            ['min' => 7.90, 'max' => 8.89, 'rate' => 35.75],
            ['min' => 8.90, 'max' => 9.89, 'rate' => 34.75],
            ['min' => 9.90, 'max' => 10.89, 'rate' => 33.25],
            ['min' => 10.90, 'max' => 11.89, 'rate' => 32.75],
            ['min' => 11.90, 'max' => 12.89, 'rate' => 31.75],
            ['min' => 12.90, 'max' => 13.89, 'rate' => 30.25],
            ['min' => 13.90, 'max' => 14.89, 'rate' => 29.25],
            ['min' => 14.90, 'max' => 16.99, 'rate' => 28.25],
            ['min' => 17.00, 'max' => 19.99, 'rate' => 27.75],
            ['min' => 20.00, 'max' => null, 'rate' => 25.75],
        ];
    }

    /**
     * Calculate cubic feet from dimensions
     *
     * @param float $length
     * @param float $width
     * @param float $height
     * @return float
     */
    public static function calculateCubicFeet(float $length, float $width, float $height): float
    {
        $cubicInches = $length * $width * $height;
        return round($cubicInches / 1728, 2);
    }
}




