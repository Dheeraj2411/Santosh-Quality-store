<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function index(Request $request): Response
    {
        $products = Product::with('category')
            ->when($request->search, fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products'   => $products->through(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'slug'        => $p->slug,
                'price'       => $p->price,
                'stock'       => $p->stock,
                'is_active'   => $p->is_active,
                'is_featured' => $p->is_featured,
                'thumbnail'   => $p->thumbnail_url,
                'category'    => $p->category->name ?? '-',
            ]),
            'categories' => Category::active()->get(['id', 'name']),
            'filters'    => $request->only(['search', 'category_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'mrp'         => 'nullable|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'stock'       => 'required|integer|min:0',
            'sku'         => 'nullable|string|max:100|unique:products',
            'barcode'     => 'nullable|string|max:255|unique:products',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'thumbnail'   => 'nullable|image|max:2048',
            'images.*'    => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->imageService->uploadProductImage($request->file('thumbnail'));
        }

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            $this->imageService->attachImagesToProduct($product->id, $request->file('images'), true);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Edit', [
            'product'    => array_merge($product->toArray(), [
                'thumbnail_url' => $product->thumbnail_url,
                'images'        => $product->images->map(fn($img) => ['id' => $img->id, 'url' => $img->url]),
            ]),
            'categories' => Category::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'mrp'         => 'nullable|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'stock'       => 'required|integer|min:0',
            'sku'         => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode'     => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'thumbnail'   => 'nullable|image|max:2048',
            'images.*'    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                $this->imageService->deleteImage($product->thumbnail);
            }
            $data['thumbnail'] = $this->imageService->uploadProductImage($request->file('thumbnail'));
        }

        $product->update($data);

        if ($request->hasFile('images')) {
            $this->imageService->attachImagesToProduct($product->id, $request->file('images'));
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->thumbnail) {
            $this->imageService->deleteImage($product->thumbnail);
        }
        foreach ($product->images as $img) {
            $this->imageService->deleteImage($img->image_path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function deleteImage(ProductImage $image)
    {
        $this->imageService->deleteImage($image->image_path);
        $image->delete();
        return back()->with('success', 'Image deleted.');
    }
}
