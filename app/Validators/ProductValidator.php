<?php

declare(strict_types=1);

namespace App\Validators;

use App\Core\BaseValidator;
use App\DTOs\ProductDTO;
use App\Repositories\ProductRepository;

class ProductValidator extends BaseValidator
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function performValidation(object $dto): void
    {
        if (!$dto instanceof ProductDTO) {
            $this->errors[] = 'DTO inválido';
            return;
        }

        $this->validateRequired($dto->name, 'Nome é obrigatório');
        $this->validateLength($dto->name, 2, 100, 'Nome deve ter entre 2 e 100 caracteres');

        $this->validateRequired($dto->code, 'Código é obrigatório');
        $this->validateLength($dto->code, 2, 50, 'Código deve ter entre 2 e 50 caracteres');

        if ($this->repository->existsByCode($dto->code, $dto->id)) {
            $this->errors[] = 'Código já está em uso';
        }

        $this->validateRange($dto->price, 0.01, 999999.99, 'Preço deve estar entre R$ 0,01 e R$ 999.999,99');
        $this->validateRange($dto->cost, 0.01, 999999.99, 'Custo deve estar entre R$ 0,01 e R$ 999.999,99');

        if ($dto->stock < 0) {
            $this->errors[] = 'Estoque não pode ser negativo';
        }

        if ($dto->minStock < 0) {
            $this->errors[] = 'Estoque mínimo não pode ser negativo';
        }

        if ($dto->categoryId <= 0) {
            $this->errors[] = 'Categoria é obrigatória';
        }

        if ($dto->supplierId <= 0) {
            $this->errors[] = 'Fornecedor é obrigatório';
        }
    }
}
