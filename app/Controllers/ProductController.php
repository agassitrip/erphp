<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\ProductService;
use App\Exceptions\BaseException;

class ProductController extends BaseController
{
    private ProductService $service;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->service = new ProductService(
            new \App\Repositories\ProductRepository($container->get('database')),
            new \App\Validators\ProductValidator(new \App\Repositories\ProductRepository($container->get('database')))
        );
    }

    public function index(): void
    {
        $this->requireAdmin();
        $products = $this->service->getAll();
        $this->view('products/index', ['products' => $products]);
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
            $this->flashSuccess('Produto excluído com sucesso!');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
        }
        $this->redirect('/produtos');
    }

    private function handleCreate(): void
    {
        try {
            $this->service->create($_POST);
            $this->flashSuccess('Produto criado com sucesso!');
            $this->redirect('/produtos');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showForm();
        }
    }

    private function handleUpdate(int $id): void
    {
        try {
            $this->service->update($id, $_POST);
            $this->flashSuccess('Produto atualizado com sucesso!');
            $this->redirect('/produtos');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showEditForm($id);
        }
    }

    private function showForm(): void
    {
        $this->view('products/form');
    }

    private function showEditForm(int $id): void
    {
        try {
            $product = $this->service->getById($id);
            $this->view('products/form', ['product' => $product]);
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/produtos');
        }
    }
}
