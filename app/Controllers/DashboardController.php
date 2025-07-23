<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\SaleService;
use App\Services\ProductService;
use App\Services\CustomerService;

class DashboardController extends BaseController
{
    private SaleService $saleService;
    private ProductService $productService;
    private CustomerService $customerService;

    public function __construct($container)
    {
        parent::__construct($container);
        $database = $container->get('database');
        
        $this->saleService = new SaleService(
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
        
        $todayStats = $this->saleService->getTodayStats();
        $lowStockProducts = $this->productService->getLowStock();
        $totalCustomers = count($this->customerService->getAll());
        $totalProducts = count($this->productService->getAll());
        
        $this->view('dashboard/index', [
            'todayStats' => $todayStats,
            'lowStockProducts' => $lowStockProducts,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts
        ]);
    }
}
