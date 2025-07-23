<?php

declare(strict_types=1);

namespace App\DTOs;

class CustomerDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $document,
        public readonly string $address,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zipCode,
        public readonly bool $active
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            phone: $data['phone'] ?? '',
            document: $data['document'] ?? '',
            address: $data['address'] ?? '',
            city: $data['city'] ?? '',
            state: $data['state'] ?? '',
            zipCode: $data['zip_code'] ?? '',
            active: $data['active'] ?? true
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'document' => $this->document,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
            'active' => $this->active ? 1 : 0
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        return $data;
    }
}
