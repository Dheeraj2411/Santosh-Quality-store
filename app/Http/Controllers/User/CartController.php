<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        return Inertia::render('Cart/Index', [
            'cart' => $this->cartService->getCartWithProducts(),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'integer|min:1|max:50',
        ]);

        $this->cartService->addItem(
            $request->product_id,
            $request->quantity ?? 1
        );

        return response()->json([
            'message' => 'Added to cart',
            'cart'    => $this->cartService->getCartWithProducts(),
        ]);
    }

    public function update(Request $request, int $productId): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:50']);
        $this->cartService->updateQuantity($productId, $request->quantity);

        return response()->json([
            'cart' => $this->cartService->getCartWithProducts(),
        ]);
    }

    public function remove(int $productId): JsonResponse
    {
        $this->cartService->removeItem($productId);
        return response()->json([
            'message' => 'Item removed',
            'cart'    => $this->cartService->getCartWithProducts(),
        ]);
    }

    public function getCart(): JsonResponse
    {
        return response()->json($this->cartService->getCartWithProducts());
    }
}
