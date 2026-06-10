<?php

namespace App\Services;

class GpsValidationService
{
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2)
            + cos($lat1Rad) * cos($lat2Rad)
            * sin($deltaLng / 2) * sin($deltaLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isWithinRadius(
        float $lat,
        float $lng,
        float $schoolLat,
        float $schoolLng,
        int $radiusMeters,
    ): bool {
        return $this->calculateDistance($lat, $lng, $schoolLat, $schoolLng) <= $radiusMeters;
    }
}
