<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\SaleService;
use App\Services\ProductService;
use App\Services\CustomerService;
use App\Exceptions\BaseException;

class SaleController extends BaseController
{
    private SaleService $service;
    private ProductService $productService;
    private CustomerService $customerService;

    public function __construct($container)
    {
        parent::__construct($container);
        $database = $container->get('database');
        
        $this->service = new SaleService(
            new \App\Repositories\SaleRepository($database),
            new \App\Repositories\ProductRepository($database)
        );
        
        $this->productService = new ProductService(
            new \App\Repositories\ProductRepository($database),
            new \App\Validators\ProductValidator(new \App\Repositories\ProductRepository($database))
        );
        
        $this->customerService = new CustomerService(
            new \App\Repositories\CustomerRepository($database),
            new \App\Validators\CustomerValidator(new \App\Repositories\CustomerRepository($database))
        );
    }

    public function index(): void
    {
        $this->requireAdmin();
        $sales = $this->service->getAll();
        $this->view('sales/index', ['sales' => $sales]);
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

    public function show(?string $id = null): void
    {
        $this->requireAdmin();
        try {
            $sale = $this->service->getById((int)$id);
            $this->view('sales/view', ['sale' => $sale]);
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/vendas');
        }
    }

    private function handleCreate(): void
    {
        try {
            $_POST['user_id'] = $_SESSION['user_id'];
            $this->service->create($_POST);
            $this->flashSuccess('Venda realizada com sucesso!');
            $this->redirect('/vendas');
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showForm();
        }
    }

    private function showForm(): void
    {
        $customers = $this->customerService->getAll();
        $this->view('sales/form', ['customers' => $customers]);
    }
}
