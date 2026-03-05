<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            $isFavorited = true;
        }

        return response()->json([
            'favorited' => $isFavorited,
            'message'   => $isFavorited ? 'Added to favorites' : 'Removed from favorites',
        ]);
    }
}
