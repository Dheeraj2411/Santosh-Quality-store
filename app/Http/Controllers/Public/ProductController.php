<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['category_id', 'min_price', 'max_price', 'search', 'sort', 'direction']);
        $products = $this->productRepo->getAll($filters, 16);

        $categories = Category::active()->orderBy('sort_order')->get(['id', 'name', 'slug']);

        return Inertia::render('Products/Index', [
            'products'   => $products->through(fn($p) => [
                'id'        => $p->id,
                'name'      => $p->name,
                'slug'      => $p->slug,
                'price'     => $p->price,
                'mrp'       => $p->mrp,
                'unit'      => $p->unit,
                'thumbnail' => $p->thumbnail_url,
                'in_stock'  => $p->isInStock(),
                'discount'  => $p->discount_percentage,
                'category'  => $p->category->name ?? '',
            ]),
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = $this->productRepo->findBySlug($slug);

        if (!$product) {
            abort(404);
        }

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)->get()
            ->map(fn($p) => [
                'id'        => $p->id,
                'name'      => $p->name,
                'slug'      => $p->slug,
                'price'     => $p->price,
                'mrp'       => $p->mrp,
                'thumbnail' => $p->thumbnail_url,
                'in_stock'  => $p->isInStock(),
            ]);

        return Inertia::render('Products/Show', [
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'description' => $product->description,
                'price'       => $product->price,
                'mrp'         => $product->mrp,
                'unit'        => $product->unit,
                'stock'       => $product->stock,
                'in_stock'    => $product->isInStock(),
                'discount'    => $product->discount_percentage,
                'thumbnail'   => $product->thumbnail_url,
                'images'      => $product->images->map(fn($img) => ['id' => $img->id, 'url' => $img->url]),
                'category'    => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name, 'slug' => $product->category->slug] : null,
                'avg_rating'  => $product->average_rating,
                'reviews'     => $product->reviews->map(fn($r) => [
                    'id'        => $r->id,
                    'rating'    => $r->rating,
                    'title'     => $r->title,
                    'body'      => $r->body,
                    'user_name' => $r->user->name,
                    'user_avatar' => $r->user->avatar_url,
                    'created_at' => $r->created_at->diffForHumans(),
                ]),
            ],
            'related' => $related,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(8)
            ->get(['id', 'name', 'slug', 'price', 'thumbnail']);

        return response()->json($products->map(fn($p) => [
            'id'        => $p->id,
            'name'      => $p->name,
            'slug'      => $p->slug,
            'price'     => $p->price,
            'thumbnail' => $p->thumbnail_url,
        ]));
    }
}
