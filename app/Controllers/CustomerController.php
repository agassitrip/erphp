<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CustomerService;
use App\Exceptions\BaseException;

class CustomerController extends BaseController
{
    private CustomerService $service;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->service = new CustomerService(
            new \App\Repositories\CustomerRepository($container->get('database')),
            new \App\Validators\CustomerValidator(new \App\Repositories\CustomerRepository($container->get('database')))
        );
    }

    public function index(): void
    {
        $this->requireAdmin();
        $customers = $this->service->getAll();
        $this->view('customers/index', ['customers' => $customers]);
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
            $this->flashSuccess('Cliente excluído com sucesso!');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
        }
        $this->redirect('/clientes');
    }

    private function handleCreate(): void
    {
        try {
            $this->service->create($_POST);
            $this->flashSuccess('Cliente criado com sucesso!');
            $this->redirect('/clientes');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showForm();
        }
    }

    private function handleUpdate(int $id): void
    {
        try {
            $this->service->update($id, $_POST);
            $this->flashSuccess('Cliente atualizado com sucesso!');
            $this->redirect('/clientes');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showEditForm($id);
        }
    }

    private function showForm(): void
    {
        $this->view('customers/form');
    }

    private function showEditForm(int $id): void
    {
        try {
            $customer = $this->service->getById($id);
            $this->view('customers/form', ['customer' => $customer]);
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/clientes');
        }
    }
}
