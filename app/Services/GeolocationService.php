<?php

namespace App\Services;

use App\Models\Setting;

class GeolocationService
{
    public const DEFAULT_LOCATIONS = [
        1 => [
            'key_prefix' => 'location_1',
            'name' => 'Workplace',
            'address' => 'Shop Apartment, No. 20-1F (1), Jalan Orkid 4, Taman Orkid, 43200 Cheras, Selangor, Malaysia.',
            'lat' => 3.0783092,
            'lng' => 101.7664661,
            'radius' => 500.0,
        ],
        2 => [
            'key_prefix' => 'location_2',
            'name' => 'Home / Demo',
            'address' => 'The Sky Residensi, Taman Shamelin Perkasa, 56100 Kuala Lumpur, Malaysia.',
            'lat' => 3.1247527,
            'lng' => 101.7374223,
            'radius' => 500.0,
        ],
        3 => [
            'key_prefix' => 'location_3',
            'name' => 'College / Demo',
            'address' => 'Universiti Poly-Tech Malaysia (UPTM), Jalan 6/91, Taman Shamelin Perkasa, 56100 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur.',
            'lat' => 3.1276441,
            'lng' => 101.7373364,
            'radius' => 500.0,
        ],
    ];

    /**
     * Retrieve all configured locations from Settings with fallbacks.
     */
    public static function getAllLocations(): array
    {
        $locations = [];
        $legacyWorkplaceLat = Setting::getVal('workplace_lat');
        $legacyWorkplaceLng = Setting::getVal('workplace_lng');
        $legacyWorkplaceRadius = Setting::getVal('workplace_radius');

        // Check if legacy workplace is explicitly disabled/unconfigured with '0'
        $legacyDisabled = ($legacyWorkplaceLat !== null && (float) $legacyWorkplaceLat === 0.0);

        foreach (self::DEFAULT_LOCATIONS as $index => $default) {
            $prefix = $default['key_prefix'];

            $name = Setting::getVal("{$prefix}_name", $default['name']);
            $address = Setting::getVal("{$prefix}_address", $default['address']);

            if ($index === 1) {
                $latVal = $legacyWorkplaceLat ?? Setting::getVal("{$prefix}_lat", (string) $default['lat']);
                $lngVal = $legacyWorkplaceLng ?? Setting::getVal("{$prefix}_lng", (string) $default['lng']);
                $radiusVal = $legacyWorkplaceRadius ?? Setting::getVal("{$prefix}_radius", (string) $default['radius']);
            } else {
                if ($legacyDisabled) {
                    $latVal = '0';
                    $lngVal = '0';
                    $radiusVal = '0';
                } else {
                    $latVal = Setting::getVal("{$prefix}_lat", (string) $default['lat']);
                    $lngVal = Setting::getVal("{$prefix}_lng", (string) $default['lng']);
                    $radiusVal = Setting::getVal("{$prefix}_radius", (string) $default['radius']);
                }
            }

            $locations[$index] = [
                'id' => $index,
                'name' => (string) $name,
                'address' => (string) $address,
                'lat' => (float) $latVal,
                'lng' => (float) $lngVal,
                'radius' => (float) $radiusVal,
            ];
        }

        return $locations;
    }

    /**
     * Retrieve only active locations (non-zero lat/lng, positive radius).
     */
    public static function getActiveLocations(): array
    {
        return array_values(array_filter(self::getAllLocations(), function ($loc) {
            return $loc['lat'] != 0.0 && $loc['lng'] != 0.0 && $loc['radius'] > 0.0;
        }));
    }

    /**
     * Calculate great-circle distance between two points in meters using Haversine formula.
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth radius in meters
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * Validate user GPS coordinates against all active allowed locations.
     */
    public static function validateLocation($latInput, $lngInput): array
    {
        $activeLocations = self::getActiveLocations();

        // FAIL CLOSED: If no allowed location is configured or active
        if (empty($activeLocations)) {
            return [
                'valid' => false,
                'error' => 'Workplace GPS geofence is not configured. Please contact the administrator to set the laboratory coordinates.',
                'matched_location' => null,
                'nearest_location' => null,
                'distances' => [],
            ];
        }

        // Check if GPS coordinates are provided and numeric
        if ($latInput === null || $lngInput === null || $latInput === '' || $lngInput === '' || !is_numeric($latInput) || !is_numeric($lngInput)) {
            return [
                'valid' => false,
                'error' => 'GPS location permission is required. Attendance cannot be recorded without verified workplace location.',
                'matched_location' => null,
                'nearest_location' => null,
                'distances' => [],
            ];
        }

        $lat = (float) $latInput;
        $lng = (float) $lngInput;

        // Validate incoming latitude and longitude ranges
        if ($lat < -90.0 || $lat > 90.0 || $lng < -180.0 || $lng > 180.0) {
            return [
                'valid' => false,
                'error' => 'Invalid GPS coordinates provided. Latitude must be between -90 and 90, and longitude between -180 and 180.',
                'matched_location' => null,
                'nearest_location' => null,
                'distances' => [],
            ];
        }

        $distances = [];
        $matchedLocation = null;
        $nearestLocation = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($activeLocations as $loc) {
            $dist = self::calculateDistance($loc['lat'], $loc['lng'], $lat, $lng);
            $item = [
                'id' => $loc['id'],
                'name' => $loc['name'],
                'address' => $loc['address'],
                'distance' => round($dist, 1),
                'radius' => $loc['radius'],
                'inside' => ($dist <= $loc['radius']),
            ];
            $distances[] = $item;

            if ($dist < $minDistance) {
                $minDistance = $dist;
                $nearestLocation = $item;
            }

            if ($item['inside'] && $matchedLocation === null) {
                $matchedLocation = $item;
            }
        }

        if ($matchedLocation !== null) {
            return [
                'valid' => true,
                'error' => null,
                'matched_location' => $matchedLocation,
                'nearest_location' => $nearestLocation,
                'distances' => $distances,
            ];
        }

        $distRounded = round($nearestLocation['distance']);
        $radiusRounded = round($nearestLocation['radius']);
        $nearestName = $nearestLocation['name'];

        return [
            'valid' => false,
            'error' => "Outside Allowed Location: You are outside all allowed locations (Nearest: {$nearestName}, {$distRounded}m away, max allowed: {$radiusRounded}m). Attendance is not allowed.",
            'matched_location' => null,
            'nearest_location' => $nearestLocation,
            'distances' => $distances,
        ];
    }
}
