<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderRepositoryInterface $orderRepo) {}

    public function index(Request $request): Response
    {
        $orders = $this->orderRepo->getAll($request->only(['status', 'search']));

        return Inertia::render('Admin/Orders/Index', [
            'orders'  => $orders->through(fn($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'user_name'    => $o->user->name ?? '-',
                'total'        => $o->total,
                'status'       => $o->status,
                'badge_color'  => $o->status_badge_color,
                'created_at'   => $o->created_at->format('d M Y'),
            ]),
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['items.product', 'user', 'address']);
        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id'              => $order->id,
                'order_number'    => $order->order_number,
                'status'          => $order->status,
                'payment_method'  => $order->payment_method,
                'payment_status'  => $order->payment_status,
                'subtotal'        => $order->subtotal,
                'delivery_charge' => $order->delivery_charge,
                'discount'        => $order->discount,
                'total'           => $order->total,
                'notes'           => $order->notes,
                'shipping_address' => $order->shipping_address,
                'user'            => $order->user ? ['name' => $order->user->name, 'email' => $order->user->email] : null,
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

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled']);
        $this->orderRepo->updateStatus($order->id, $request->status);
        return back()->with('success', 'Order status updated to ' . ucfirst($request->status) . '.');
    }
}
