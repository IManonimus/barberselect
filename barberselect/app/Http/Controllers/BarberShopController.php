<?php

namespace App\Http\Controllers;

use App\Models\BarberShop;
use App\Services\NearbyBarberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarberShopController extends Controller
{
    public function index(): JsonResponse
    {
        $shops = BarberShop::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'hours', 'phone', 'rating', 'lat', 'lng']);

        return response()->json([
            'data' => $shops,
        ]);
    }

    public function nearby(Request $request, NearbyBarberService $service): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'integer', 'min:500', 'max:15000'],
        ]);

        $shops = $service->find(
            (float) $validated['lat'],
            (float) $validated['lng'],
            (int) ($validated['radius'] ?? 5000),
        );

        return response()->json([
            'data' => $shops,
            'meta' => [
                'lat' => (float) $validated['lat'],
                'lng' => (float) $validated['lng'],
                'radius_m' => (int) ($validated['radius'] ?? 5000),
                'count' => count($shops),
                'live' => true,
            ],
        ]);
    }
}
