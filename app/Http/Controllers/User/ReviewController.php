<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'title'      => 'nullable|string|max:255',
            'body'       => 'nullable|string|max:2000',
        ]);

        $existing = Review::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->exists();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            'user_id'    => auth()->id(),
            'product_id' => $request->product_id,
            'rating'     => $request->rating,
            'title'      => $request->title,
            'body'       => $request->body,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Review submitted! It will be visible after approval.');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
