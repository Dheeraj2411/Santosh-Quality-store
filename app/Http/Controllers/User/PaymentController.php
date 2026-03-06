<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * Create a Razorpay order and return the order_id to the frontend.
     */
    public function createRazorpayOrder(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart();

        if (empty($cart['items'])) {
            return response()->json(['error' => 'Cart is empty'], 422);
        }

        $amountInPaise = (int) round($cart['total'] * 100); // Razorpay uses paise

        $keyId     = config('services.razorpay.key_id');
        $keySecret = config('services.razorpay.key_secret');

        $payload = [
            'amount'          => $amountInPaise,
            'currency'        => 'INR',
            'receipt'         => 'rcpt_' . uniqid(),
            'payment_capture' => 1,
        ];

        $response = $this->callRazorpay('POST', 'orders', $keyId, $keySecret, $payload);

        if (!isset($response['id'])) {
            return response()->json(['error' => 'Failed to create Razorpay order. Check API keys.'], 500);
        }

        return response()->json([
            'razorpay_order_id' => $response['id'],
            'amount'            => $amountInPaise,
            'currency'          => 'INR',
            'key_id'            => $keyId,
            'user'              => [
                'name'  => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone ?? '',
            ],
        ]);
    }

    /**
     * Verify Razorpay signature and create the order.
     */
    public function verifyAndPlaceOrder(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
            'address_id'          => 'required|integer|exists:addresses,id',
            'notes'               => 'nullable|string|max:500',
        ]);

        // Verify signature
        $keySecret = config('services.razorpay.key_secret');
        $expectedSignature = hash_hmac(
            'sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            $keySecret
        );

        if (!hash_equals($expectedSignature, $request->razorpay_signature)) {
            return response()->json(['error' => 'Payment verification failed. Please contact support.'], 422);
        }

        // Place the order with payment details
        try {
            $cart    = $this->cartService->getCartWithProducts();
            $address = \App\Models\Address::where('id', $request->address_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            if (empty($cart['items'])) {
                return response()->json(['error' => 'Cart is empty.'], 422);
            }

            $order = $this->orderRepository->create([
                'order_number'        => \App\Models\Order::generateOrderNumber(),
                'user_id'             => $request->user()->id,
                'address_id'          => $address->id,
                'subtotal'            => $cart['subtotal'],
                'delivery_charge'     => $cart['delivery_charge'],
                'total'               => $cart['total'],
                'payment_method'      => 'razorpay',
                'payment_status'      => 'paid',
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'notes'               => $request->notes,
                'status'              => 'processing',
                'shipping_address'    => $address->toArray(),
            ]);

            // Create order items and decrement stock
            foreach ($cart['items'] as $item) {
                \App\Models\OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'total'        => $item['line_total'],
                ]);
                \App\Models\Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            $this->cartService->clearCart();

            return response()->json([
                'success'       => true,
                'message'       => 'Payment successful! Your order has been placed.',
                'order_number'  => $order->order_number,
                'redirect'      => route('user.orders.show', $order->id),
                'whatsapp_url'  => $this->buildWhatsAppUrl($order, $cart['items'], $address, $request->user()),
            ]);
        } catch (\Exception $e) {
            \Log::error('Razorpay order placement failed: ' . $e->getMessage());
            return response()->json(['error' => 'Order placement failed. Payment was captured — contact support.'], 500);
        }
    }

    /**
     * Handle COD order placement (existing flow).
     */
    public function placeCodOrder(Request $request)
    {
        $request->validate([
            'address_id' => 'required|integer|exists:addresses,id',
            'notes'      => 'nullable|string|max:500',
        ]);

        try {
            $cart    = $this->cartService->getCartWithProducts();
            $address = \App\Models\Address::where('id', $request->address_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            if (empty($cart['items'])) {
                return back()->with('error', 'Your cart is empty.');
            }

            $order = $this->orderRepository->create([
                'order_number'     => \App\Models\Order::generateOrderNumber(),
                'user_id'          => $request->user()->id,
                'address_id'       => $address->id,
                'subtotal'         => $cart['subtotal'],
                'delivery_charge'  => $cart['delivery_charge'],
                'total'            => $cart['total'],
                'payment_method'   => 'cod',
                'payment_status'   => 'pending',
                'notes'            => $request->notes,
                'status'           => 'pending',
                'shipping_address' => $address->toArray(),
            ]);

            // Create order items and decrement stock
            foreach ($cart['items'] as $item) {
                \App\Models\OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'total'        => $item['line_total'],
                ]);
                \App\Models\Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            $this->cartService->clearCart();

            $whatsappUrl = $this->buildWhatsAppUrl($order, $cart['items'], $address, $request->user());

            return redirect()->route('user.orders.show', $order->id)
                ->with('success', 'Order placed! We will deliver soon. 🎉')
                ->with('whatsapp_url', $whatsappUrl);
        } catch (\Exception $e) {
            \Log::error('COD order failed: ' . $e->getMessage());
            return back()->with('error', 'Could not place order. Please try again.');
        }
    }

    /**
     * Minimal HTTP helper to call Razorpay REST API using built-in cURL.
     */
    private function callRazorpay(string $method, string $endpoint, string $keyId, string $keySecret, array $data = []): array
    {
        $url = "https://api.razorpay.com/v1/{$endpoint}";
        $ch  = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => "{$keyId}:{$keySecret}",
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    /**
     * Build a WhatsApp click-to-chat URL with order details.
     */
    private function buildWhatsAppUrl($order, array $items, $address, $user): string
    {
        // Use store phone from settings, fallback to hardcoded
        $storePhone = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'store_phone')->value('value') ?? '919650671568';
        // Strip non-digits
        $storePhone = preg_replace('/\D/', '', $storePhone);

        $itemLines = [];
        foreach ($items as $item) {
            $itemLines[] = "• {$item['name']} × {$item['quantity']} = ₹{$item['line_total']}";
        }

        $message = "🛒 *New Order from {$user->name}*\n"
            . "📋 Order: #{$order->order_number}\n"
            . "💰 Total: ₹{$order->total}\n"
            . "💳 Payment: " . strtoupper($order->payment_method) . "\n\n"
            . "*Items:*\n" . implode("\n", $itemLines) . "\n\n"
            . "📍 *Delivery Address:*\n"
            . "{$address->name}, {$address->line1}" . ($address->line2 ? ", {$address->line2}" : '')
            . ", {$address->city}, {$address->state} {$address->pincode}\n\n"
            . ($order->notes ? "📝 Notes: {$order->notes}\n" : '')
            . "📞 Phone: {$user->phone}";

        return 'https://wa.me/' . $storePhone . '?text=' . urlencode($message);
    }
}
