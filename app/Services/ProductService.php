<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductDTO;
use App\Repositories\ProductRepository;
use App\Validators\ProductValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class ProductService
{
    private ProductRepository $repository;
    private ProductValidator $validator;

    public function __construct(ProductRepository $repository, ProductValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function getAll(): array
    {
        return $this->repository->findWithDetails();
    }

    public function getById(int $id): array
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new NotFoundException('Produto não encontrado');
        }
        return $product;
    }

    public function create(array $data): int
    {
        $dto = ProductDTO::fromArray($data);

        if (!$this->validator->validate($dto)) {
            throw new ValidationException($this->validator->getErrors());
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->getById($id);
        $data['id'] = $id;

        $dto = ProductDTO::fromArray(array_merge($existing, $data));

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

    public function getLowStock(): array
    {
        return $this->repository->findLowStock();
    }

    public function searchByNameOrCode(string $term): array
    {
        return $this->repository->searchByNameOrCode($term);
    }

    public function updateStock(int $productId, int $quantity): void
    {
        $this->getById($productId);
        $this->repository->updateStock($productId, $quantity);
    }
}
