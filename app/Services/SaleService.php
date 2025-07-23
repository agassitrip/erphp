<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\SaleDTO;
use App\Repositories\SaleRepository;
use App\Repositories\ProductRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class SaleService
{
    private SaleRepository $repository;
    private ProductRepository $productRepository;

    public function __construct(SaleRepository $repository, ProductRepository $productRepository)
    {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function getAll(): array
    {
        return $this->repository->findWithDetails();
    }

    public function getById(int $id): array
    {
        $sale = $this->repository->findByIdWithDetails($id);
        if (!$sale) {
            throw new NotFoundException('Venda não encontrada');
        }
        return $sale;
    }

    public function create(array $data): int
    {
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new ValidationException(['Itens da venda são obrigatórios']);
        }

        $this->validateItems($data['items']);

        $dto = SaleDTO::fromArray($data);
        $saleId = $this->repository->create($dto->toArray());

        $this->createSaleItems($saleId, $data['items']);
        $this->updateProductStock($data['items']);

        return $saleId;
    }

    public function getTodayStats(): array
    {
        return $this->repository->getTodayStats();
    }

    public function getSalesReport(string $startDate, string $endDate): array
    {
        return $this->repository->getSalesReport($startDate, $endDate);
    }

    private function validateItems(array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || empty($item['price'])) {
                throw new ValidationException(['Dados dos itens inválidos']);
            }

            $product = $this->productRepository->findById((int)$item['product_id']);
            if (!$product) {
                throw new ValidationException(['Produto não encontrado']);
            }

            if ($product['stock'] < (int)$item['quantity']) {
                throw new ValidationException(["Estoque insuficiente para o produto {$product['name']}"]);
            }
        }
    }

    private function createSaleItems(int $saleId, array $items): void
    {
        foreach ($items as $item) {
            $this->repository->getConnection()->prepare("
                INSERT INTO sale_items (sale_id, product_id, quantity, price, total, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ")->execute([
                $saleId,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['quantity'] * $item['price'],
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ]);
        }
    }

    private function updateProductStock(array $items): void
    {
        foreach ($items as $item) {
            $this->productRepository->updateStock((int)$item['product_id'], -(int)$item['quantity']);
        }
    }
}
