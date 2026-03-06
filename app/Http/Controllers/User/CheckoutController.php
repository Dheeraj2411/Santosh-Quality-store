<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
    ) {}

    public function index(): Response|\Illuminate\Http\RedirectResponse
    {
        $cart = $this->cartService->getCartWithProducts();

        if (empty($cart['items'])) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        $addresses = auth()->user()->addresses()->get();

        return Inertia::render('Checkout', [
            'cart'      => $cart,
            'addresses' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id'     => 'required|integer|exists:addresses,id',
            'payment_method' => 'required|in:cod,online',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Ensure address belongs to user
        $address = auth()->user()->addresses()->findOrFail($request->address_id);

        try {
            $order = $this->orderService->placeOrder(
                auth()->id(),
                $address->id,
                $request->only(['payment_method', 'notes'])
            );

            return redirect()->route('user.orders.show', $order->id)
                ->with('success', "Order #{$order->order_number} placed successfully!");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
