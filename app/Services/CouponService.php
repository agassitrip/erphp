<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;
use DateTime;

class CouponService
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function validateCoupon(string $code, float $orderValue): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM cupons 
             WHERE code = ? AND active = 1 
             AND valid_from <= NOW() 
             AND valid_until >= NOW()
             AND min_order_value <= ?
             AND (usage_limit IS NULL OR used_count < usage_limit)"
        );
        $stmt->execute([$code, $orderValue]);
        $coupon = $stmt->fetch();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Cupom inválido, expirado ou não atende aos critérios mínimos',
                'discount' => 0
            ];
        }

        $discount = $this->calculateDiscount($coupon, $orderValue);

        return [
            'valid' => true,
            'message' => 'Cupom válido',
            'discount' => $discount,
            'coupon' => $coupon
        ];
    }

    public function applyCoupon(string $code): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE cupons SET used_count = used_count + 1 WHERE code = ?"
        );
        return $stmt->execute([$code]);
    }

    private function calculateDiscount(array $coupon, float $orderValue): float
    {
        if ($coupon['type'] === 'percentage') {
            return ($orderValue * $coupon['value']) / 100;
        } else {
            return (float) $coupon['value'];
        }
    }

    public function getAllCoupons(): array
    {
        $stmt = $this->db->prepare(
            "SELECT *, 
             CASE 
                WHEN valid_until < NOW() THEN 'Expirado'
                WHEN valid_from > NOW() THEN 'Agendado'
                WHEN usage_limit IS NOT NULL AND used_count >= usage_limit THEN 'Esgotado'
                ELSE 'Ativo'
             END as status_display
             FROM cupons 
             ORDER BY created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createCoupon(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO cupons (code, type, value, min_order_value, valid_from, valid_until, usage_limit, active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([
            strtoupper($data['code']),
            $data['type'],
            $data['value'],
            $data['min_order_value'] ?? 0,
            $data['valid_from'],
            $data['valid_until'],
            $data['usage_limit'] ?? null,
            $data['active'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }
}