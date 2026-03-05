<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $reviews = Review::with(['user', 'product'])
            ->when($request->status === 'pending', fn($q) => $q->where('is_approved', false))
            ->when($request->status === 'approved', fn($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews->through(fn($r) => [
                'id'           => $r->id,
                'rating'       => $r->rating,
                'title'        => $r->title,
                'body'         => $r->body,
                'is_approved'  => $r->is_approved,
                'user_name'    => $r->user->name ?? '-',
                'product_name' => $r->product->name ?? 'Deleted',
                'created_at'   => $r->created_at->format('d M Y'),
            ]),
            'filters' => $request->only(['status']),
        ]);
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Review approved.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
