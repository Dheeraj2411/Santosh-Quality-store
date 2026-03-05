<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 16): LengthAwarePaginator
    {
        $query = Product::with(['category', 'images'])
            ->active()
            ->when(isset($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
            ->when(isset($filters['min_price']),   fn($q) => $q->where('price', '>=', $filters['min_price']))
            ->when(isset($filters['max_price']),   fn($q) => $q->where('price', '<=', $filters['max_price']))
            ->when(isset($filters['search']),      fn($q) => $q->where('name', 'like', '%' . $filters['search'] . '%'))
            ->when(isset($filters['featured']),    fn($q) => $q->featured());

        $sortField = $filters['sort'] ?? 'sort_order';
        $sortDir   = $filters['direction'] ?? 'asc';

        return $query->orderBy($sortField, $sortDir)->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?Product
    {
        return Product::with(['category', 'images', 'reviews.user'])->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['category', 'images', 'reviews.user'])->where('slug', $slug)->first();
    }

    public function getFeatured(int $limit = 8): Collection
    {
        return Product::with(['category'])->active()->featured()->limit($limit)->get();
    }

    public function search(string $query, int $perPage = 16): LengthAwarePaginator
    {
        return Product::with('category')
            ->active()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product->fresh();
    }

    public function delete(int $id): bool
    {
        return Product::findOrFail($id)->delete();
    }
}
