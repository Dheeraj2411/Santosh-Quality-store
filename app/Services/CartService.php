<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'santosh_cart';

    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function addItem(int $productId, int $quantity = 1, array $meta = []): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                ...$meta,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $quantity;
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    public function removeItem(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clearCart(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function getCount(): int
    {
        return array_sum(array_column($this->getCart(), 'quantity'));
    }

    public function getCartWithProducts(): array
    {
        $cartItems = $this->getCart();
        if (empty($cartItems)) {
            return ['items' => [], 'subtotal' => 0, 'total' => 0, 'count' => 0];
        }

        $productIds = array_keys($cartItems);
        $products   = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

        $items    = [];
        $subtotal = 0;

        foreach ($cartItems as $productId => $cartItem) {
            if (!isset($products[$productId])) continue;
            $product    = $products[$productId];
            $lineTotal  = $product->price * $cartItem['quantity'];
            $subtotal  += $lineTotal;
            $items[]    = [
                'product_id'  => $productId,
                'name'        => $product->name,
                'price'       => $product->price,
                'thumbnail'   => $product->thumbnail_url,
                'unit'        => $product->unit,
                'quantity'    => $cartItem['quantity'],
                'line_total'  => $lineTotal,
                'stock'       => $product->stock,
            ];
        }

        $deliveryCharge = $subtotal >= 500 ? 0 : 40;

        return [
            'items'           => $items,
            'subtotal'        => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'total'           => $subtotal + $deliveryCharge,
            'count'           => array_sum(array_column($cartItems, 'quantity')),
        ];
    }
}
