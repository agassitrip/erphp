<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\UserService;
use App\Exceptions\BaseException;

class UserController extends BaseController
{
    private UserService $service;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->service = new UserService(
            new \App\Repositories\UserRepository($container->get('database')),
            new \App\Validators\UserValidator(new \App\Repositories\UserRepository($container->get('database')))
        );
    }

    public function index(): void
    {
        $this->requireAdmin();
        $users = $this->service->getAll();
        $this->view('users/index', ['users' => $users]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        if ($this->isPost()) {
            $this->handleCreate();
            return;
        }
        $this->showForm();
    }

    public function edit(?string $id = null): void
    {
        $this->requireAdmin();
        if ($this->isPost()) {
            $this->handleUpdate((int)$id);
            return;
        }
        $this->showEditForm((int)$id);
    }

    public function delete(?string $id = null): void
    {
        $this->requireAdmin();
        try {
            $this->service->delete((int)$id);
            $this->flashSuccess('Usuário excluído com sucesso!');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
        }
        $this->redirect('/usuarios');
    }

    private function handleCreate(): void
    {
        try {
            $this->service->create($_POST);
            $this->flashSuccess('Usuário criado com sucesso!');
            $this->redirect('/usuarios');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showForm();
        }
    }

    private function handleUpdate(int $id): void
    {
        try {
            $this->service->update($id, $_POST);
            $this->flashSuccess('Usuário atualizado com sucesso!');
            $this->redirect('/usuarios');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showEditForm($id);
        }
    }

    private function showForm(): void
    {
        $this->view('users/form');
    }

    private function showEditForm(int $id): void
    {
        try {
            $user = $this->service->getById($id);
            $this->view('users/form', ['user' => $user]);
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/usuarios');
        }
    }
}
