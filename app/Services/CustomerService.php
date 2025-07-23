<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CustomerDTO;
use App\Repositories\CustomerRepository;
use App\Validators\CustomerValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class CustomerService
{
    private CustomerRepository $repository;
    private CustomerValidator $validator;

    public function __construct(CustomerRepository $repository, CustomerValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function getById(int $id): array
    {
        $customer = $this->repository->findById($id);
        if (!$customer) {
            throw new NotFoundException('Cliente não encontrado');
        }
        return $customer;
    }

    public function create(array $data): int
    {
        $dto = CustomerDTO::fromArray($data);

        if (!$this->validator->validate($dto)) {
            throw new ValidationException($this->validator->getErrors());
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->getById($id);
        $data['id'] = $id;

        $dto = CustomerDTO::fromArray(array_merge($existing, $data));

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

    public function getWithPurchaseHistory(int $id): array
    {
        return $this->repository->findWithPurchaseHistory($id);
    }
}
