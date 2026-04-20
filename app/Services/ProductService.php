<?php

namespace App\Services;

use App\DTO\Product\ProductData;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

final class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function paginatePublic(int $perPage, ?int $categoryId, ?string $search)
    {
        return $this->products->paginateWithFilters($perPage, $categoryId, $search);
    }

    public function find(int $id): Product
    {
        return $this->products->findOrFail($id);
    }

    public function create(ProductData $data): Product
    {
        return $this->products->create([
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'category_id' => $data->categoryId,
        ]);
    }

    public function update(Product $product, ProductData $data): Product
    {
        return $this->products->update($product, [
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'category_id' => $data->categoryId,
        ]);
    }

    public function delete(Product $product): void
    {
        $this->products->delete($product);
    }
}
