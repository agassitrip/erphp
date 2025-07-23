<?php

declare(strict_types=1);

namespace App\DTOs;

class SaleDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $customerId,
        public readonly int $userId,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $total,
        public readonly string $paymentMethod,
        public readonly string $status,
        public readonly array $items
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            customerId: (int)($data['customer_id'] ?? 0),
            userId: (int)($data['user_id'] ?? 0),
            subtotal: (float)($data['subtotal'] ?? 0),
            discount: (float)($data['discount'] ?? 0),
            total: (float)($data['total'] ?? 0),
            paymentMethod: $data['payment_method'] ?? 'cash',
            status: $data['status'] ?? 'completed',
            items: $data['items'] ?? []
        );
    }

    public function toArray(): array
    {
        $data = [
            'customer_id' => $this->customerId,
            'user_id' => $this->userId,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'payment_method' => $this->paymentMethod,
            'status' => $this->status
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        return $data;
    }
}
