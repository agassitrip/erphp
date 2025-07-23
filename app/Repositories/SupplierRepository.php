<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;

class SupplierRepository extends BaseRepository
{
    protected string $table = 'suppliers';

    public function findByDocument(string $document): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE document = ? AND active = 1");
        $stmt->execute([$document]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function existsByDocument(string $document, ?int $excludeId = null): bool
    {
        return $this->exists('document', $document, $excludeId);
    }

    public function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        return $this->exists('email', $email, $excludeId);
    }

    public function findWithProductCount(): array
    {
        $sql = "
            SELECT s.*, COUNT(p.id) as product_count
            FROM {$this->table} s
            LEFT JOIN products p ON s.id = p.supplier_id AND p.active = 1
            WHERE s.active = 1
            GROUP BY s.id
            ORDER BY s.name
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
