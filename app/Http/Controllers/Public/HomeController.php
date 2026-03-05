<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\StoreSetting;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featuredProducts = Product::with(['category'])
            ->active()->featured()->limit(8)->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'slug'        => $p->slug,
                'price'       => $p->price,
                'mrp'         => $p->mrp,
                'unit'        => $p->unit,
                'thumbnail'   => $p->thumbnail_url,
                'in_stock'    => $p->isInStock(),
                'discount'    => $p->discount_percentage,
                'category'    => $p->category->name ?? '',
            ]);

        $categories = Category::active()
            ->orderBy('sort_order')
            ->withCount('products')
            ->get()
            ->map(fn($c) => [
                'id'            => $c->id,
                'name'          => $c->name,
                'slug'          => $c->slug,
                'image'         => $c->image ? asset('storage/'.$c->image) : null,
                'products_count' => $c->products_count,
            ]);

        $reviews = Review::with(['user', 'product'])
            ->where('is_approved', true)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'rating'       => $r->rating,
                'title'        => $r->title,
                'body'         => $r->body,
                'user_name'    => $r->user->name,
                'user_avatar'  => $r->user->avatar_url,
                'product_name' => $r->product->name ?? '',
                'created_at'   => $r->created_at->diffForHumans(),
            ]);

        $settings = StoreSetting::all_settings();

        return Inertia::render('Welcome', compact('featuredProducts', 'categories', 'reviews', 'settings'));
    }
}
