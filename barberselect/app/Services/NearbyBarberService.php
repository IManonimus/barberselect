<?php

namespace App\Services;

use App\Models\BarberShop;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NearbyBarberService
{
    private const EARTH_RADIUS_KM = 6371;

    public function find(float $lat, float $lng, int $radiusMeters = 5000): array
    {
        $cacheKey = sprintf('nearby:%.3f:%.3f:%d', $lat, $lng, $radiusMeters);

        return Cache::remember($cacheKey, 45, function () use ($lat, $lng, $radiusMeters) {
            $radiusKm = $radiusMeters / 1000;

            $dbShops = $this->fromDatabase($lat, $lng, $radiusKm);
            $osmShops = $this->fromOpenStreetMap($lat, $lng, $radiusMeters);

            return $this->mergeShops($dbShops, $osmShops)
                ->sortBy('distance_km')
                ->values()
                ->take(15)
                ->all();
        });
    }

    private function fromDatabase(float $lat, float $lng, float $radiusKm): Collection
    {
        return BarberShop::query()
            ->where('is_active', true)
            ->get(['id', 'name', 'address', 'hours', 'phone', 'rating', 'lat', 'lng'])
            ->map(function (BarberShop $shop) use ($lat, $lng) {
                $distanceKm = $this->haversine($lat, $lng, (float) $shop->lat, (float) $shop->lng);

                return [
                    'id' => 'db-'.$shop->id,
                    'name' => $shop->name,
                    'address' => $shop->address,
                    'hours' => $shop->hours,
                    'phone' => $shop->phone,
                    'rating' => $shop->rating,
                    'lat' => (float) $shop->lat,
                    'lng' => (float) $shop->lng,
                    'distance_km' => round($distanceKm, 3),
                    'source' => 'partner',
                ];
            })
            ->filter(fn (array $shop) => $shop['distance_km'] <= max($radiusKm, 30));
    }

    private function fromOpenStreetMap(float $lat, float $lng, int $radiusMeters): Collection
    {
        $query = sprintf(
            '[out:json][timeout:8];('.
            'node["shop"="hairdresser"](around:%d,%F,%F);'.
            'node["amenity"="barber"](around:%d,%F,%F);'.
            'node["shop"="barber"](around:%d,%F,%F);'.
            ');out body;',
            $radiusMeters, $lat, $lng,
            $radiusMeters, $lat, $lng,
            $radiusMeters, $lat, $lng,
        );

        $endpoints = [
            'https://overpass.kumi.systems/api/interpreter',
            'https://overpass-api.de/api/interpreter',
        ];

        foreach ($endpoints as $endpoint) {
            try {
                $response = Http::timeout(8)
                    ->connectTimeout(4)
                    ->asForm()
                    ->post($endpoint, ['data' => $query]);

                if (! $response->ok()) {
                    continue;
                }

                $elements = $response->json('elements', []);

                return collect($elements)
                    ->map(function (array $element) use ($lat, $lng) {
                        $shopLat = $element['lat'] ?? null;
                        $shopLng = $element['lon'] ?? null;

                        if ($shopLat === null || $shopLng === null) {
                            return null;
                        }

                        $tags = $element['tags'] ?? [];
                        $name = $tags['name'] ?? $tags['brand'] ?? 'Barbershop';
                        $address = collect([
                            $tags['addr:street'] ?? null,
                            $tags['addr:city'] ?? $tags['addr:suburb'] ?? null,
                        ])->filter()->implode(', ');

                        return [
                            'id' => 'osm-'.$element['id'],
                            'name' => $name,
                            'address' => $address !== '' ? $address : 'Lokasi dari OpenStreetMap',
                            'hours' => $tags['opening_hours'] ?? null,
                            'phone' => $tags['phone'] ?? $tags['contact:phone'] ?? null,
                            'rating' => null,
                            'lat' => (float) $shopLat,
                            'lng' => (float) $shopLng,
                            'distance_km' => round($this->haversine($lat, $lng, (float) $shopLat, (float) $shopLng), 3),
                            'source' => 'osm',
                        ];
                    })
                    ->filter();
            } catch (\Throwable) {
                continue;
            }
        }

        return collect();
    }

    private function mergeShops(Collection $dbShops, Collection $osmShops): Collection
    {
        $merged = $dbShops->values();

        foreach ($osmShops as $osmShop) {
            $isDuplicate = $merged->contains(function (array $existing) use ($osmShop) {
                return $this->haversine(
                    $existing['lat'],
                    $existing['lng'],
                    $osmShop['lat'],
                    $osmShop['lng']
                ) < 0.05;
            });

            if (! $isDuplicate) {
                $merged->push($osmShop);
            }
        }

        return $merged;
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return self::EARTH_RADIUS_KM * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
