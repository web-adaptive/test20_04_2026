<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginateWithFilters(int $perPage, ?int $categoryId, ?string $search): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->when($categoryId, fn (Builder $q) => $q->where('category_id', $categoryId))
            ->when($search, function (Builder $q) use ($search) {
                $term = '%'.addcslashes($search, '%_\\').'%';
                $q->where(function (Builder $inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOrFail(int $id): Product
    {
        return Product::query()
            ->with('category')
            ->findOrFail($id);
    }

    public function create(array $attributes): Product
    {
        $product = new Product($attributes);
        $product->save();

        return $product->load('category');
    }

    public function update(Product $product, array $attributes): Product
    {
        $product->fill($attributes);
        $product->save();

        return $product->fresh(['category']);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
