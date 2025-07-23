<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\SaleService;
use App\Services\ProductService;

class ReportController extends BaseController
{
    private SaleService $saleService;
    private ProductService $productService;

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
    }

    public function sales(): void
    {
        $this->requireAdmin();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $salesReport = $this->saleService->getSalesReport($startDate, $endDate);
        
        $this->view('reports/sales', [
            'salesReport' => $salesReport,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function stock(): void
    {
        $this->requireAdmin();
        
        $products = $this->productService->getAll();
        $lowStockProducts = $this->productService->getLowStock();
        
        $this->view('reports/stock', [
            'products' => $products,
            'lowStockProducts' => $lowStockProducts
        ]);
    }

    public function financial(): void
    {
        $this->requireAdmin();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $salesReport = $this->saleService->getSalesReport($startDate, $endDate);
        
        $this->view('reports/financial', [
            'salesReport' => $salesReport,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
