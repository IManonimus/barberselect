<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $catalogs = Catalog::with('category')->orderBy('name')->get();

        return view('katalog', [
            'catalogs' => $catalogs,
        ]);
    }

    public function show(Catalog $catalog)
    {
        return view('catalog-detail', [
            'catalog' => $catalog->load('category'),
        ]);
    }

    public function apiIndex(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:200'],
            'take' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $take = (int) ($validated['take'] ?? 50);
        $q = trim((string) ($validated['q'] ?? ''));
        $categoryId = $validated['category_id'] ?? null;

        $items = Catalog::query()
            ->with('category')
            ->when($categoryId, fn ($b) => $b->where('category_id', $categoryId))
            ->when($q !== '', function ($b) use ($q) {
                $b->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('face_shape', 'like', "%{$q}%")
                        ->orWhere('hair_type', 'like', "%{$q}%")
                        ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$q}%"));
                });
            })
            ->orderBy('name')
            ->limit($take)
            ->get()
            ->map(function (Catalog $c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'care_level' => $c->care_level,
                    'face_shape' => $c->face_shape,
                    'hair_type' => $c->hair_type,
                    'tips' => $c->tips,
                    'image_url' => $c->image_url,
                    'category' => $c->category ? [
                        'id' => $c->category->id,
                        'name' => $c->category->name,
                        'image_url' => $c->category->image_url,
                    ] : null,
                ];
            })
            ->values();

        return response()->json([
            'data' => $items,
        ]);
    }

    public function apiShow(Request $request, Catalog $catalog)
    {
        $catalog->load('category');
        return response()->json([
            'catalog' => [
                'id' => $catalog->id,
                'name' => $catalog->name,
                'description' => $catalog->description,
                'care_level' => $catalog->care_level,
                'face_shape' => $catalog->face_shape,
                'hair_type' => $catalog->hair_type,
                'tips' => $catalog->tips,
                'image_url' => $catalog->image_url,
                'category' => $catalog->category ? [
                    'id' => $catalog->category->id,
                    'name' => $catalog->category->name,
                    'image_url' => $catalog->category->image_url,
                ] : null,
            ],
        ]);
    }

    protected function ensureAdmin(Request $request): void
    {
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }
    }

    public function apiAdminIndex(Request $request)
    {
        $this->ensureAdmin($request);
        return $this->apiIndex($request);
    }

    public function apiAdminStore(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'care_level' => ['nullable', 'string', 'max:255'],
            'face_shape' => ['nullable', 'string', 'max:255'],
            'hair_type' => ['nullable', 'string', 'max:255'],
            'tips' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $catalog = Catalog::create($data);
        return response()->json([
            'message' => 'Katalog dibuat.',
            'catalog' => $catalog->load('category'),
        ], 201);
    }

    public function apiAdminUpdate(Request $request, Catalog $catalog)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'care_level' => ['nullable', 'string', 'max:255'],
            'face_shape' => ['nullable', 'string', 'max:255'],
            'hair_type' => ['nullable', 'string', 'max:255'],
            'tips' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $catalog->update($data);
        return response()->json([
            'message' => 'Katalog diperbarui.',
            'catalog' => $catalog->load('category'),
        ]);
    }

    public function apiAdminDestroy(Request $request, Catalog $catalog)
    {
        $this->ensureAdmin($request);
        $catalog->delete();
        return response()->json([
            'message' => 'Katalog dihapus.',
        ]);
    }
}
