<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCatalogController extends Controller
{
    protected function ensureAdmin(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $catalogs = Catalog::with('category')->orderBy('name')->get();

        return view('admin.catalogs.index', [
            'catalogs' => $catalogs,
        ]);
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);

        $categories = Category::orderBy('name')->get();

        return view('admin.catalogs.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
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

        Activity::create([
            'user_id' => $request->user()?->id,
            'action' => 'admin.catalog.created',
            'meta' => [
                'catalog_id' => $catalog->id,
                'name' => $catalog->name,
                'category_id' => $catalog->category_id,
            ],
        ]);

        return redirect()->route('admin.catalogs.index')->with('status', 'Katalog berhasil ditambahkan.');
    }

    public function edit(Request $request, Catalog $catalog)
    {
        $this->ensureAdmin($request);

        $categories = Category::orderBy('name')->get();

        return view('admin.catalogs.edit', [
            'catalog' => $catalog,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Catalog $catalog)
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

        Activity::create([
            'user_id' => $request->user()?->id,
            'action' => 'admin.catalog.updated',
            'meta' => [
                'catalog_id' => $catalog->id,
                'name' => $catalog->name,
                'category_id' => $catalog->category_id,
            ],
        ]);

        return redirect()->route('admin.catalogs.index')->with('status', 'Katalog berhasil diperbarui.');
    }

    public function destroy(Request $request, Catalog $catalog)
    {
        $this->ensureAdmin($request);

        $catalogId = $catalog->id;
        $catalogName = $catalog->name;
        $categoryId = $catalog->category_id;
        $catalog->delete();

        Activity::create([
            'user_id' => $request->user()?->id,
            'action' => 'admin.catalog.deleted',
            'meta' => [
                'catalog_id' => $catalogId,
                'name' => $catalogName,
                'category_id' => $categoryId,
            ],
        ]);

        return back()->with('status', 'Katalog berhasil dihapus.');
    }
}
