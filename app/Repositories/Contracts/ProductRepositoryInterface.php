<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 16): LengthAwarePaginator;
    public function findById(int $id): ?\App\Models\Product;
    public function findBySlug(string $slug): ?\App\Models\Product;
    public function getFeatured(int $limit = 8): Collection;
    public function search(string $query, int $perPage = 16): LengthAwarePaginator;
    public function create(array $data): \App\Models\Product;
    public function update(int $id, array $data): \App\Models\Product;
    public function delete(int $id): bool;
}
