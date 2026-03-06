<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Favorite;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->with('items')->latest()->limit(5)->get();

        return Inertia::render('Dashboard/Profile', [
            'user'          => $user->only(['id', 'name', 'email', 'phone', 'avatar_url']),
            'recent_orders' => $recentOrders->map(fn($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'status'       => $o->status,
                'total'        => $o->total,
                'items_count'  => $o->items->count(),
                'created_at'   => $o->created_at->format('d M Y'),
            ]),
            'stats' => [
                'orders'    => $user->orders()->count(),
                'favorites' => $user->favorites()->count(),
                'reviews'   => $user->reviews()->count(),
                'addresses' => $user->addresses()->count(),
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only(['name', 'phone']));

        return back()->with('success', 'Profile updated successfully.');
    }

    public function orders(): Response
    {
        $orders = auth()->user()->orders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return Inertia::render('Dashboard/Orders', [
            'orders' => $orders->through(fn($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'status'       => $o->status,
                'total'        => $o->total,
                'items_count'  => $o->items->count(),
                'created_at'   => $o->created_at->format('d M Y'),
                'badge_color'  => $o->status_badge_color,
            ]),
        ]);
    }

    public function showOrder(Order $order): Response
    {
        // Ensure the user can only view their own orders
        abort_if($order->user_id !== auth()->id(), 403);

        return Inertia::render('Dashboard/OrderDetail', [
            'order' => [
                'id'              => $order->id,
                'order_number'    => $order->order_number,
                'status'          => $order->status,
                'payment_method'  => $order->payment_method,
                'payment_status'  => $order->payment_status,
                'subtotal'        => $order->subtotal,
                'delivery_charge' => $order->delivery_charge,
                'total'           => $order->total,
                'notes'           => $order->notes,
                'shipping_address' => $order->shipping_address,
                'items'           => $order->items->map(fn($i) => [
                    'id'           => $i->id,
                    'product_name' => $i->product_name,
                    'price'        => $i->price,
                    'quantity'     => $i->quantity,
                    'total'        => $i->total,
                ]),
                'created_at'      => $order->created_at->format('d M Y, H:i'),
                'badge_color'     => $order->status_badge_color,
            ],
        ]);
    }

    public function favorites(): Response
    {
        $favorites = auth()->user()->favorites()
            ->with('product.category')
            ->paginate(12);

        return Inertia::render('Dashboard/Favorites', [
            'favorites' => $favorites->through(fn($f) => [
                'id'        => $f->product->id,
                'name'      => $f->product->name,
                'slug'      => $f->product->slug,
                'price'     => $f->product->price,
                'mrp'       => $f->product->mrp,
                'thumbnail' => $f->product->thumbnail_url,
                'in_stock'  => $f->product->isInStock(),
                'category'  => $f->product->category->name ?? '',
            ]),
        ]);
    }

    public function addresses(): Response
    {
        return Inertia::render('Dashboard/Addresses', [
            'addresses' => auth()->user()->addresses()->get(),
        ]);
    }

    public function reviews(): Response
    {
        $reviews = auth()->user()->reviews()->with('product')->paginate(10);

        return Inertia::render('Dashboard/Reviews', [
            'reviews' => $reviews->through(fn($r) => [
                'id'           => $r->id,
                'rating'       => $r->rating,
                'title'        => $r->title,
                'body'         => $r->body,
                'is_approved'  => $r->is_approved,
                'product_name' => $r->product->name ?? 'Deleted Product',
                'product_slug' => $r->product->slug ?? null,
                'created_at'   => $r->created_at->format('d M Y'),
            ]),
        ]);
    }
}
