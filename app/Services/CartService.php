<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

class CartService
{
    private PDO $db;
    private CouponService $couponService;

    public function __construct(Database $database, CouponService $couponService = null)
    {
        $this->db = $database->getConnection();
        $this->couponService = $couponService ?? new CouponService($database);
    }

    public function addToCart(int $productId, int $quantity = 1, ?int $variationId = null): bool
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $key = $productId . ($variationId ? "_$variationId" : '');
        
        $existingQuantity = isset($_SESSION['cart'][$key]) ? $_SESSION['cart'][$key]['quantity'] : 0;
        $totalQuantity = $existingQuantity + $quantity;
        
        if (!$this->checkStock($productId, $totalQuantity, $variationId)) {
            return false;
        }

        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$key] = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'quantity' => $quantity
            ];
        }

        return true;
    }

    public function removeFromCart(string $key): void
    {
        if (isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
        }
    }

    public function updateQuantity(string $key, int $quantity): bool
    {
        if (!isset($_SESSION['cart'][$key])) {
            return false;
        }

        $item = $_SESSION['cart'][$key];
        
        if (!$this->checkStock($item['product_id'], $quantity, $item['variation_id'])) {
            return false;
        }

        if ($quantity <= 0) {
            $this->removeFromCart($key);
        } else {
            $_SESSION['cart'][$key]['quantity'] = $quantity;
        }

        return true;
    }

    public function getCartItems(): array
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return [];
        }

        $items = [];
        foreach ($_SESSION['cart'] as $key => $item) {
            $product = $this->getProductWithStock($item['product_id'], $item['variation_id']);
            if ($product) {
                $items[$key] = array_merge($item, $product);
                $items[$key]['total'] = $product['final_price'] * $item['quantity'];
            }
        }

        return $items;
    }

    public function getCartSummary(): array
    {
        $items = $this->getCartItems();
        $subtotal = array_sum(array_column($items, 'total'));
        $itemCount = array_sum(array_column($items, 'quantity'));

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
            'shipping' => $this->calculateShipping($subtotal),
            'total' => $subtotal + $this->calculateShipping($subtotal)
        ];
    }

    public function calculateShipping(float $subtotal): float
    {
        if ($subtotal >= 200.00) {
            return 0.00; // Frete grátis
        } elseif ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }

    public function applyCoupon(string $couponCode): array
    {
        $summary = $this->getCartSummary();
        $couponResult = $this->couponService->validateCoupon($couponCode, $summary['subtotal']);

        if ($couponResult['valid']) {
            $_SESSION['cart_coupon'] = [
                'code' => $couponCode,
                'discount' => $couponResult['discount']
            ];
        }

        return $couponResult;
    }

    public function removeCoupon(): void
    {
        unset($_SESSION['cart_coupon']);
    }

    public function getCartWithCoupon(): array
    {
        $summary = $this->getCartSummary();
        
        if (isset($_SESSION['cart_coupon'])) {
            $coupon = $_SESSION['cart_coupon'];
            $summary['coupon_code'] = $coupon['code'];
            $summary['coupon_discount'] = $coupon['discount'];
            $summary['total_with_discount'] = $summary['total'] - $coupon['discount'];
        } else {
            $summary['coupon_code'] = null;
            $summary['coupon_discount'] = 0;
            $summary['total_with_discount'] = $summary['total'];
        }

        return $summary;
    }

    public function clearCart(): void
    {
        unset($_SESSION['cart']);
        unset($_SESSION['cart_coupon']);
    }

    private function checkStock(int $productId, int $quantity, ?int $variationId = null): bool
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'estoque'");
            $estoqueExists = $stmt->rowCount() > 0;

            if ($estoqueExists) {
                $sql = "SELECT quantity FROM estoque WHERE product_id = ?";
                $params = [$productId];

                if ($variationId) {
                    $sql .= " AND variation_id = ?";
                    $params[] = $variationId;
                } else {
                    $sql .= " AND variation_id IS NULL";
                }

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $stock = $stmt->fetch();

                return $stock && $stock['quantity'] >= $quantity;
            } else {
                $stmt = $this->db->prepare("SELECT stock FROM products WHERE id = ? AND active = 1");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                return $product && $product['stock'] >= $quantity;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAvailableStock(int $productId, ?int $variationId = null): int
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'estoque'");
            $estoqueExists = $stmt->rowCount() > 0;

            $cartQuantity = 0;
            $key = $productId . ($variationId ? "_$variationId" : '');
            if (isset($_SESSION['cart'][$key])) {
                $cartQuantity = $_SESSION['cart'][$key]['quantity'];
            }

            if ($estoqueExists) {
                $sql = "SELECT quantity FROM estoque WHERE product_id = ?";
                $params = [$productId];

                if ($variationId) {
                    $sql .= " AND variation_id = ?";
                    $params[] = $variationId;
                } else {
                    $sql .= " AND variation_id IS NULL";
                }

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $stock = $stmt->fetch();

                return $stock ? max(0, $stock['quantity'] - $cartQuantity) : 0;
            } else {
                $stmt = $this->db->prepare("SELECT stock FROM products WHERE id = ? AND active = 1");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                return $product ? max(0, $product['stock'] - $cartQuantity) : 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getProductWithStock(int $productId, ?int $variationId = null): ?array
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'estoque'");
            $estoqueExists = $stmt->rowCount() > 0;

            if ($estoqueExists) {
                $sql = "SELECT p.*, COALESCE(e.quantity, p.stock) as stock_quantity, 
                        COALESCE(pv.price_adjustment, 0) as price_adjustment,
                        COALESCE(pv.name, p.name) as display_name,
                        (p.price + COALESCE(pv.price_adjustment, 0)) as final_price
                        FROM products p
                        LEFT JOIN estoque e ON p.id = e.product_id
                        LEFT JOIN product_variations pv ON p.id = pv.product_id AND pv.id = ?
                        WHERE p.id = ? AND p.active = 1";
            } else {
                $sql = "SELECT p.*, p.stock as stock_quantity, 
                        0 as price_adjustment,
                        p.name as display_name,
                        p.price as final_price
                        FROM products p
                        WHERE p.id = ? AND p.active = 1";
            }

            $stmt = $this->db->prepare($sql);
            if ($estoqueExists) {
                $stmt->execute([$variationId, $productId]);
            } else {
                $stmt->execute([$productId]);
            }
            
            $product = $stmt->fetch();
            if ($product) {
                $product['available_stock'] = $this->getAvailableStock($productId, $variationId);
            }
            
            return $product ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }
}