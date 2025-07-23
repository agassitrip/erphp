<?php

declare(strict_types=1);

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role,
        public readonly bool $active
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            role: $data['role'] ?? 'user',
            active: $data['active'] ?? true
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'active' => $this->active ? 1 : 0
        ];

        if (!empty($this->password)) {
            $data['password'] = password_hash($this->password, PASSWORD_BCRYPT);
        }

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        return $data;
    }
}
