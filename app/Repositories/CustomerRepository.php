<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;

class CustomerRepository extends BaseRepository
{
    protected string $table = 'customers';

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

    public function findWithPurchaseHistory(int $customerId): array
    {
        $sql = "
            SELECT c.*, 
                   COUNT(s.id) as total_purchases,
                   COALESCE(SUM(s.total), 0) as total_spent,
                   MAX(s.created_at) as last_purchase
            FROM {$this->table} c
            LEFT JOIN sales s ON c.id = s.customer_id
            WHERE c.id = ? AND c.active = 1
            GROUP BY c.id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$customerId]);
        $result = $stmt->fetch();
        return $result ?: [];
    }
}
