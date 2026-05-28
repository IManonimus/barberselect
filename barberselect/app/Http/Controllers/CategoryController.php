<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        return view('kategori', [
            'categories' => $categories,
        ]);
    }

    public function apiIndex(Request $request)
    {
        $items = Category::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'image_url' => $c->image_url,
            ])
            ->values();

        return response()->json([
            'data' => $items,
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
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $category = Category::create($data);
        return response()->json([
            'message' => 'Kategori dibuat.',
            'category' => $category,
        ], 201);
    }

    public function apiAdminUpdate(Request $request, Category $category)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $category->update($data);
        return response()->json([
            'message' => 'Kategori diperbarui.',
            'category' => $category,
        ]);
    }

    public function apiAdminDestroy(Request $request, Category $category)
    {
        $this->ensureAdmin($request);
        $category->delete();
        return response()->json([
            'message' => 'Kategori dihapus.',
        ]);
    }
}
