<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CartService $cartService,
    ) {}

    public function placeOrder(int $userId, int $addressId, array $data): Order
    {
        $cart = $this->cartService->getCartWithProducts();

        if (empty($cart['items'])) {
            throw new \Exception('Cart is empty.');
        }

        return DB::transaction(function () use ($userId, $addressId, $data, $cart) {
            // Get the address snapshot
            $address = \App\Models\Address::findOrFail($addressId);
            $addressSnapshot = [
                'name'    => $address->name,
                'phone'   => $address->phone,
                'line1'   => $address->line1,
                'line2'   => $address->line2,
                'city'    => $address->city,
                'state'   => $address->state,
                'pincode' => $address->pincode,
                'country' => $address->country,
            ];

            $order = $this->orderRepository->create([
                'order_number'    => Order::generateOrderNumber(),
                'user_id'         => $userId,
                'address_id'      => $addressId,
                'payment_method'  => $data['payment_method'] ?? 'cod',
                'payment_status'  => 'pending',
                'subtotal'        => $cart['subtotal'],
                'discount'        => 0,
                'delivery_charge' => $cart['delivery_charge'],
                'total'           => $cart['total'],
                'notes'           => $data['notes'] ?? null,
                'shipping_address' => $addressSnapshot,
            ]);

            foreach ($cart['items'] as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'total'        => $item['line_total'],
                ]);

                // Decrement stock
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            $this->cartService->clearCart();

            return $order->fresh(['items', 'address']);
        });
    }

    public function cancelOrder(Order $order): Order
    {
        if (!in_array($order->status, ['pending', 'processing'])) {
            throw new \Exception('Order cannot be cancelled at this stage.');
        }

        // Restore stock
        foreach ($order->items as $item) {
            Product::where('id', $item->product_id)->increment('stock', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);
        return $order->fresh();
    }
}
