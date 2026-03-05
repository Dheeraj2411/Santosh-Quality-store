<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAllForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['items.product', 'address', 'user'])->find($id);
    }

    public function findByNumber(string $number): ?Order
    {
        return Order::with(['items.product', 'address', 'user'])->where('order_number', $number)->first();
    }

    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Order::with(['user', 'items'])
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['search']), fn($q) => $q->where('order_number', 'like', '%'.$filters['search'].'%'))
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function updateStatus(int $id, string $status): Order
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);
        if ($status === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }
        return $order->fresh();
    }

    public function getRevenueStats(): array
    {
        $today = now()->toDateString();
        $month = now()->format('Y-m');

        return [
            'today'          => Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('total'),
            'this_month'     => Order::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])->where('status','!=','cancelled')->sum('total'),
            'total_revenue'  => Order::where('status', '!=', 'cancelled')->sum('total'),
            'total_orders'   => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ];
    }
}
