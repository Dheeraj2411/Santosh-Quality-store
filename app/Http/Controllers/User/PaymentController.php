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
            $cart    = $this->cartService->getCart();
            $address = \App\Models\Address::where('id', $request->address_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $order = $this->orderRepository->create([
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
                'status'              => 'processing', // Auto-move to processing since paid
                'shipping_address'    => $address->toArray(),
            ], $cart['items']);

            $this->cartService->clear();

            return response()->json([
                'success'       => true,
                'message'       => 'Payment successful! Your order has been placed.',
                'order_number'  => $order->order_number,
                'redirect'      => route('user.orders.show', $order->id),
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
            $cart    = $this->cartService->getCart();
            $address = \App\Models\Address::where('id', $request->address_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            if (empty($cart['items'])) {
                return back()->with('error', 'Your cart is empty.');
            }

            $order = $this->orderRepository->create([
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
            ], $cart['items']);

            $this->cartService->clear();

            return redirect()->route('user.orders.show', $order->id)
                ->with('success', 'Order placed! We will deliver soon. 🎉');
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
}
