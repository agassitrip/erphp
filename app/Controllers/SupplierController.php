<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\SupplierService;
use App\Exceptions\BaseException;

class SupplierController extends BaseController
{
    private SupplierService $service;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->service = new SupplierService(
            new \App\Repositories\SupplierRepository($container->get('database')),
            new \App\Validators\SupplierValidator(new \App\Repositories\SupplierRepository($container->get('database')))
        );
    }

    public function index(): void
    {
        $this->requireAdmin();
        $suppliers = $this->service->getAll();
        $this->view('suppliers/index', ['suppliers' => $suppliers]);
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
            $this->flashSuccess('Fornecedor excluído com sucesso!');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
        }
        $this->redirect('/fornecedores');
    }

    private function handleCreate(): void
    {
        try {
            $this->service->create($_POST);
            $this->flashSuccess('Fornecedor criado com sucesso!');
            $this->redirect('/fornecedores');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showForm();
        }
    }

    private function handleUpdate(int $id): void
    {
        try {
            $this->service->update($id, $_POST);
            $this->flashSuccess('Fornecedor atualizado com sucesso!');
            $this->redirect('/fornecedores');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showEditForm($id);
        }
    }

    private function showForm(): void
    {
        $this->view('suppliers/form');
    }

    private function showEditForm(int $id): void
    {
        try {
            $supplier = $this->service->getById($id);
            $this->view('suppliers/form', ['supplier' => $supplier]);
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/fornecedores');
        }
    }
}
