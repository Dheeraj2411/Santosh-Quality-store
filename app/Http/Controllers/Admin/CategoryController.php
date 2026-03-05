<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->paginate(20);
        return Inertia::render('Admin/Categories/Index', ['categories' => $categories]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:2048',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,'.$category->id,
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:2048',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with products.');
        }
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
