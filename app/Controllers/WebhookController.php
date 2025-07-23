<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use PDO;

class WebhookController extends BaseController
{
    public function updateOrderStatus(): void
    {
        if (!$this->isPost()) {
            http_response_code(405);
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data) {
            http_response_code(400);
            $this->json(['error' => 'Invalid JSON']);
            return;
        }

        $orderId = $data['order_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$orderId || !$status) {
            http_response_code(400);
            $this->json(['error' => 'Missing order_id or status']);
            return;
        }

        try {
            $db = $this->container->get('database')->getConnection();

            if (strtolower($status) === 'cancelado' || strtolower($status) === 'cancelled') {
                $this->cancelOrder($db, $orderId);
                $this->json(['message' => 'Order cancelled and removed successfully']);
            } else {
                $this->updateOrderStatusInDb($db, $orderId, $status);
                $this->json(['message' => 'Order status updated successfully']);
            }

        } catch (\Exception $e) {
            error_log("Webhook error: " . $e->getMessage());
            http_response_code(500);
            $this->json(['error' => 'Internal server error']);
        }
    }

    private function cancelOrder(PDO $db, int $orderId): void
    {
        $db->beginTransaction();

        try {
            $stmt = $db->prepare(
                "SELECT si.product_id, si.quantity 
                 FROM sale_items si 
                 JOIN sales s ON si.sale_id = s.id 
                 WHERE s.id = ?"
            );
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll();

            foreach ($items as $item) {
                $stmt = $db->prepare(
                    "UPDATE estoque SET quantity = quantity + ? 
                     WHERE product_id = ?"
                );
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }

            $stmt = $db->prepare("DELETE FROM sale_items WHERE sale_id = ?");
            $stmt->execute([$orderId]);

            $stmt = $db->prepare("DELETE FROM sales WHERE id = ?");
            $stmt->execute([$orderId]);

            $db->commit();

        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private function updateOrderStatusInDb(PDO $db, int $orderId, string $status): void
    {
        $stmt = $db->prepare("SELECT id FROM sales WHERE id = ?");
        $stmt->execute([$orderId]);
        
        if (!$stmt->fetch()) {
            throw new \Exception("Order not found");
        }

        $stmt = $db->prepare(
            "UPDATE sales SET status = ?, updated_at = NOW() WHERE id = ?"
        );
        $stmt->execute([$status, $orderId]);
    }

    public function test(): void
    {
        $this->json([
            'message' => 'Webhook endpoint is working',
            'timestamp' => date('Y-m-d H:i:s'),
            'expected_format' => [
                'order_id' => 'integer',
                'status' => 'string (completed, processing, shipped, cancelled, etc.)'
            ]
        ]);
    }
}