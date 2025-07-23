<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserDTO;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class UserService
{
    private UserRepository $repository;
    private UserValidator $validator;

    public function __construct(UserRepository $repository, UserValidator $validator)
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
        $user = $this->repository->findById($id);
        if (!$user) {
            throw new NotFoundException('Usuário não encontrado');
        }
        return $user;
    }

    public function create(array $data): int
    {
        $dto = UserDTO::fromArray($data);

        if (!$this->validator->validate($dto)) {
            throw new ValidationException($this->validator->getErrors());
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->getById($id);
        $data['id'] = $id;
        
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $dto = UserDTO::fromArray(array_merge($existing, $data));

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
}
