<?php

declare(strict_types=1);

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $description,
        public readonly float $price,
        public readonly float $cost,
        public readonly int $stock,
        public readonly int $minStock,
        public readonly int $categoryId,
        public readonly int $supplierId,
        public readonly bool $active
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            code: $data['code'] ?? '',
            description: $data['description'] ?? '',
            price: (float)($data['price'] ?? 0),
            cost: (float)($data['cost'] ?? 0),
            stock: (int)($data['stock'] ?? 0),
            minStock: (int)($data['min_stock'] ?? 0),
            categoryId: (int)($data['category_id'] ?? 0),
            supplierId: (int)($data['supplier_id'] ?? 0),
            active: $data['active'] ?? true
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'price' => $this->price,
            'cost' => $this->cost,
            'stock' => $this->stock,
            'min_stock' => $this->minStock,
            'category_id' => $this->categoryId,
            'supplier_id' => $this->supplierId,
            'active' => $this->active ? 1 : 0
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        return $data;
    }
}
