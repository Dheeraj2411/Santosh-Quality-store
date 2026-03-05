<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalUsers    = User::count();
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $totalRevenue  = Order::where('status', '!=', 'cancelled')->sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'user_name'    => $o->user->name,
                'total'        => $o->total,
                'status'       => $o->status,
                'badge_color'  => $o->status_badge_color,
                'created_at'   => $o->created_at->format('d M Y'),
            ]);

        // Revenue last 7 days
        $revenue7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue7Days[] = [
                'date'    => $date->format('d M'),
                'revenue' => (float) Order::whereDate('created_at', $date->toDateString())
                    ->where('status', '!=', 'cancelled')
                    ->sum('total'),
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users'    => $totalUsers,
                'total_products' => $totalProducts,
                'total_orders'   => $totalOrders,
                'total_revenue'  => $totalRevenue,
                'pending_orders' => $pendingOrders,
            ],
            'recent_orders' => $recentOrders,
            'revenue_chart' => $revenue7Days,
        ]);
    }
}
