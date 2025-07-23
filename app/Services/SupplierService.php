<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\SupplierDTO;
use App\Repositories\SupplierRepository;
use App\Validators\SupplierValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class SupplierService
{
    private SupplierRepository $repository;
    private SupplierValidator $validator;

    public function __construct(SupplierRepository $repository, SupplierValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function getAll(): array
    {
        return $this->repository->findWithProductCount();
    }

    public function getById(int $id): array
    {
        $supplier = $this->repository->findById($id);
        if (!$supplier) {
            throw new NotFoundException('Fornecedor não encontrado');
        }
        return $supplier;
    }

    public function create(array $data): int
    {
        $dto = SupplierDTO::fromArray($data);

        if (!$this->validator->validate($dto)) {
            throw new ValidationException($this->validator->getErrors());
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->getById($id);
        $data['id'] = $id;

        $dto = SupplierDTO::fromArray(array_merge($existing, $data));

        if (!$this->validator->validate($dto)) {
            throw new ValidationException($this->validator->getErrors());
        }

        $updateData = $dto->toArray();
        unset($updateData['id']);

        $this->repository->update($id, $updateData);
    }

    public function delete(int $id): void
    {
        $this->getById($id);
        $this->repository->delete($id);
    }

    public function getAllForSelect(): array
    {
        return $this->repository->findAll();
    }
}
