<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $customers = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'customer'))
            ->when($request->search, fn($q) => $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%'))
            ->withCount('orders')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers->through(fn($u) => [
                'id'           => $u->id,
                'name'         => $u->name,
                'email'        => $u->email,
                'phone'        => $u->phone,
                'is_active'    => $u->is_active,
                'orders_count' => $u->orders_count,
                'created_at'   => $u->created_at->format('d M Y'),
            ]),
            'filters' => $request->only(['search']),
        ]);
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', $user->is_active ? 'Customer activated.' : 'Customer deactivated.');
    }
}
