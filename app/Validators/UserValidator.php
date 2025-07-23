<?php

declare(strict_types=1);

namespace App\Validators;

use App\Core\BaseValidator;
use App\DTOs\UserDTO;
use App\Repositories\UserRepository;

class UserValidator extends BaseValidator
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function performValidation(object $dto): void
    {
        if (!$dto instanceof UserDTO) {
            $this->errors[] = 'DTO inválido';
            return;
        }

        $this->validateRequired($dto->name, 'Nome é obrigatório');
        $this->validateLength($dto->name, 2, 100, 'Nome deve ter entre 2 e 100 caracteres');

        $this->validateRequired($dto->email, 'Email é obrigatório');
        $this->validateEmail($dto->email);

        if ($this->repository->existsByEmail($dto->email, $dto->id)) {
            $this->errors[] = 'Email já está em uso';
        }

        if ($dto->id === null && empty($dto->password)) {
            $this->errors[] = 'Senha é obrigatória';
        }

        if (!empty($dto->password)) {
            $this->validateLength($dto->password, 6, 255, 'Senha deve ter pelo menos 6 caracteres');
        }

        if (!in_array($dto->role, ['admin', 'user'])) {
            $this->errors[] = 'Perfil inválido';
        }
    }
}
