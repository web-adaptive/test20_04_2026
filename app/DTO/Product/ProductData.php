<?php

namespace App\DTO\Product;

final readonly class ProductData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $price,
        public int $categoryId,
    ) {}

    /**
     * @param  array{name: string, description?: string|null, price: numeric, category_id: int}  $data
     */
    public static function fromValidated(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (string) $data['price'],
            categoryId: (int) $data['category_id'],
        );
    }
}
