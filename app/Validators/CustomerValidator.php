<?php

declare(strict_types=1);

namespace App\Validators;

use App\Core\BaseValidator;
use App\DTOs\CustomerDTO;
use App\Repositories\CustomerRepository;

class CustomerValidator extends BaseValidator
{
    private CustomerRepository $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function performValidation(object $dto): void
    {
        if (!$dto instanceof CustomerDTO) {
            $this->errors[] = 'DTO inválido';
            return;
        }

        $this->validateRequired($dto->name, 'Nome é obrigatório');
        $this->validateLength($dto->name, 2, 100, 'Nome deve ter entre 2 e 100 caracteres');

        if (!empty($dto->email)) {
            $this->validateEmail($dto->email);
            if ($this->repository->existsByEmail($dto->email, $dto->id)) {
                $this->errors[] = 'Email já está em uso';
            }
        }

        $this->validateRequired($dto->phone, 'Telefone é obrigatório');
        $this->validateLength($dto->phone, 10, 15, 'Telefone deve ter entre 10 e 15 caracteres');

        $this->validateRequired($dto->document, 'CPF/CNPJ é obrigatório');
        $this->validateLength($dto->document, 11, 18, 'CPF/CNPJ inválido');

        if ($this->repository->existsByDocument($dto->document, $dto->id)) {
            $this->errors[] = 'CPF/CNPJ já está em uso';
        }

        $this->validateRequired($dto->address, 'Endereço é obrigatório');
        $this->validateRequired($dto->city, 'Cidade é obrigatória');
        $this->validateRequired($dto->state, 'Estado é obrigatório');
        $this->validateLength($dto->state, 2, 2, 'Estado deve ter 2 caracteres');
        
        $this->validateRequired($dto->zipCode, 'CEP é obrigatório');
        $this->validateLength($dto->zipCode, 8, 9, 'CEP inválido');
    }
}
