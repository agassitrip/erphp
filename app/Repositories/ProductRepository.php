<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;

class ProductRepository extends BaseRepository
{
    protected string $table = 'products';

    public function findWithDetails(): array
    {
        $sql = "
            SELECT p.*, c.name as category_name, s.name as supplier_name
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            WHERE p.active = 1
            ORDER BY p.id DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE code = ? AND active = 1");
        $stmt->execute([$code]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function existsByCode(string $code, ?int $excludeId = null): bool
    {
        return $this->exists('code', $code, $excludeId);
    }

    public function findLowStock(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE stock <= min_stock AND active = 1 ORDER BY stock ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStock(int $productId, int $quantity): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET stock = stock + ?, updated_at = ? WHERE id = ?");
        return $stmt->execute([$quantity, date('Y-m-d H:i:s'), $productId]);
    }

    public function searchByNameOrCode(string $term): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE (name LIKE ? OR code LIKE ?) AND active = 1 LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%{$term}%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}
