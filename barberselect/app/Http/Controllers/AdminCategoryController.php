<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
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

        $categories = Category::orderBy('name')->get();

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);

        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $category = Category::create($data);

        Activity::create([
            'user_id' => $request->user()?->id,
            'action' => 'admin.category.created',
            'meta' => [
                'category_id' => $category->id,
                'name' => $category->name,
            ],
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Request $request, Category $category)
    {
        $this->ensureAdmin($request);

        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $category->update($data);

        Activity::create([
            'user_id' => $request->user()?->id,
            'action' => 'admin.category.updated',
            'meta' => [
                'category_id' => $category->id,
                'name' => $category->name,
            ],
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category)
    {
        $this->ensureAdmin($request);

        $categoryId = $category->id;
        $categoryName = $category->name;
        $category->delete();

        Activity::create([
            'user_id' => $request->user()?->id,
            'action' => 'admin.category.deleted',
            'meta' => [
                'category_id' => $categoryId,
                'name' => $categoryName,
            ],
        ]);

        return back()->with('status', 'Kategori berhasil dihapus.');
    }
}
