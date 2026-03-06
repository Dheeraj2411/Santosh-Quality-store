<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    /**
     * Display the POS Scanner page.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/POS/Index');
    }

    /**
     * API: Fetch product details by scanning its barcode.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $product = Product::where('barcode', $request->barcode)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found for this barcode.',
            ], 404);
        }

        if ($product->stock <= 0) {
             return response()->json([
                'success' => false,
                'message' => 'Product is out of stock!',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'mrp' => $product->mrp,
                'stock' => $product->stock,
                'barcode' => $product->barcode,
                'image' => $product->thumbnail_url,
            ]
        ]);
    }

    /**
     * API: Process the POS Checkout, deduct inventory, and record the sale.
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:cash,upi,card',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $orderItems = [];

            // 1. Verify stock and calculate totals
            foreach ($data['items'] as $item) {
                // Lock the row to prevent race conditions during checkout
                $product = Product::lockForUpdate()->find($item['id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Only {$product->stock} available.");
                }

                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                ];

                // Deduct Inventory strictly
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // 2. Create the Order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(), // Admin is the one making the sale
                'status' => 'delivered', // POS sales are instantly delivered
                'payment_method' => $data['payment_method'],
                'payment_status' => 'paid',
                'subtotal' => $subtotal,
                'discount' => 0,
                'delivery_charge' => 0,
                'total' => $subtotal,
                'notes' => 'In-store POS Sale',
                'delivered_at' => now(),
            ]);

            // 3. Attach Items
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully! Inventory deducted.',
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
