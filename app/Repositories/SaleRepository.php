<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\BaseRepository;

class SaleRepository extends BaseRepository
{
    protected string $table = 'sales';

    public function findWithDetails(): array
    {
        $sql = "
            SELECT s.*, c.name as customer_name, u.name as user_name
            FROM {$this->table} s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE s.active = 1
            ORDER BY s.created_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByIdWithDetails(int $id): ?array
    {
        $sql = "
            SELECT s.*, c.name as customer_name, c.email as customer_email,
                   u.name as user_name
            FROM {$this->table} s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE s.id = ? AND s.active = 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getSalesReport(string $startDate, string $endDate): array
    {
        $sql = "
            SELECT DATE(created_at) as date,
                   COUNT(*) as total_sales,
                   SUM(total) as total_amount
            FROM {$this->table}
            WHERE created_at BETWEEN ? AND ?
            AND active = 1
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    public function getTodayStats(): array
    {
        $today = date('Y-m-d');
        $sql = "
            SELECT COUNT(*) as total_sales,
                   COALESCE(SUM(total), 0) as total_amount
            FROM {$this->table}
            WHERE DATE(created_at) = ? AND active = 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$today]);
        $result = $stmt->fetch();
        return $result ?: ['total_sales' => 0, 'total_amount' => 0];
    }
}
