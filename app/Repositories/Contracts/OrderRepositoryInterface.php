<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function getAllForUser(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function findById(int $id): ?\App\Models\Order;
    public function findByNumber(string $number): ?\App\Models\Order;
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): \App\Models\Order;
    public function updateStatus(int $id, string $status): \App\Models\Order;
    public function getRevenueStats(): array;
}
