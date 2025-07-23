<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Exceptions\ValidationException;

class AuthService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login(string $email, string $password): bool
    {
        if (empty($email) || empty($password)) {
            throw new ValidationException(['Email e senha são obrigatórios']);
        }

        $user = $this->repository->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            throw new ValidationException(['Credenciais inválidas']);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        return true;
    }

    public function logout(): void
    {
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
}
