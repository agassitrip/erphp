<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\CepService;
use App\Services\EmailService;
use App\Core\Database;

class ShopController extends BaseController
{
    private CartService $cartService;
    private CouponService $couponService;
    private CepService $cepService;
    private EmailService $emailService;

    public function __construct($container)
    {
        parent::__construct($container);
        $database = $this->container->get('database');
        $this->couponService = new CouponService($database);
        $this->cartService = new CartService($database, $this->couponService);
        $this->cepService = new CepService();
        $this->emailService = new EmailService();
    }

    protected function renderShop(string $view, array $data = []): void
    {
        $content = $this->renderView($view, $data);
        echo $this->renderView('shop-layout', array_merge($data, ['content' => $content]));
    }
    
    private function renderView(string $view, array $data = []): string
    {
        extract($data);
        
        ob_start();
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>View não encontrada: $view</h1>";
        }
        
        return ob_get_clean();
    }

    public function index(): void
    {
        try {
            $db = $this->container->get('database')->getConnection();
            $stmt = $db->query("SHOW TABLES LIKE 'estoque'");
            $estoqueExists = $stmt->rowCount() > 0;

            $isLoggedIn = isset($_SESSION['user_id']);
            $limit = $isLoggedIn ? '' : ' LIMIT 6';

            if ($estoqueExists) {
                $sql = "SELECT p.*, COALESCE(e.quantity, p.stock) as stock_quantity 
                        FROM products p 
                        LEFT JOIN estoque e ON p.id = e.product_id 
                        WHERE p.active = 1 AND COALESCE(e.quantity, p.stock) > 0 
                        ORDER BY p.name" . $limit;
                $stmt = $db->prepare($sql);
            } else {
                $sql = "SELECT p.*, p.stock as stock_quantity 
                        FROM products p 
                        WHERE p.active = 1 AND p.stock > 0 
                        ORDER BY p.name" . $limit;
                $stmt = $db->prepare($sql);
            }

            $stmt->execute();
            $products = $stmt->fetchAll();

            foreach ($products as &$product) {
                $product['available_stock'] = $this->cartService->getAvailableStock($product['id']);
            }

            $totalProducts = 0;
            if (!$isLoggedIn) {
                if ($estoqueExists) {
                    $stmt = $db->prepare(
                        "SELECT COUNT(*) as total FROM products p 
                         LEFT JOIN estoque e ON p.id = e.product_id 
                         WHERE p.active = 1 AND COALESCE(e.quantity, p.stock) > 0"
                    );
                } else {
                    $stmt = $db->prepare(
                        "SELECT COUNT(*) as total FROM products p 
                         WHERE p.active = 1 AND p.stock > 0"
                    );
                }
                $stmt->execute();
                $totalProducts = $stmt->fetch()['total'];
            }

            $this->renderShop('shop/index', [
                'title' => 'Loja - Teste Montink',
                'products' => $products,
                'needs_migration' => !$estoqueExists,
                'is_logged_in' => $isLoggedIn,
                'total_products' => $totalProducts
            ]);

        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erro ao carregar produtos. Execute as migrações do banco de dados.';
            $this->redirect('/migrate/status');
        }
    }

    public function addToCart(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/shop');
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $variationId = !empty($_POST['variation_id']) ? (int) $_POST['variation_id'] : null;

        if ($this->cartService->addToCart($productId, $quantity, $variationId)) {
            $_SESSION['flash_success'] = 'Produto adicionado ao carrinho!';
        } else {
            $_SESSION['flash_error'] = 'Estoque insuficiente!';
        }

        $this->redirect('/shop');
    }

    public function cart(): void
    {
        $cartData = $this->cartService->getCartWithCoupon();

        $this->renderShop('shop/cart', [
            'title' => 'Carrinho - Teste Montink',
            'cart' => $cartData
        ]);
    }

    public function updateCart(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/shop/cart');
            return;
        }

        $action = $_POST['action'] ?? '';
        $key = $_POST['key'] ?? '';

        switch ($action) {
            case 'update':
                $quantity = (int) ($_POST['quantity'] ?? 1);
                if ($this->cartService->updateQuantity($key, $quantity)) {
                    $_SESSION['flash_success'] = 'Carrinho atualizado!';
                } else {
                    $_SESSION['flash_error'] = 'Erro ao atualizar carrinho!';
                }
                break;

            case 'remove':
                $this->cartService->removeFromCart($key);
                $_SESSION['flash_success'] = 'Item removido do carrinho!';
                break;
        }

        $this->redirect('/shop/cart');
    }

    public function applyCoupon(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/shop/cart');
            return;
        }

        $couponCode = $_POST['coupon_code'] ?? '';
        $result = $this->cartService->applyCoupon($couponCode);

        if ($result['valid']) {
            $_SESSION['flash_success'] = 'Cupom aplicado: ' . $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }

        $this->redirect('/shop/cart');
    }

    public function removeCoupon(): void
    {
        $this->cartService->removeCoupon();
        $_SESSION['flash_success'] = 'Cupom removido!';
        $this->redirect('/shop/cart');
    }

    public function checkout(): void
    {
        $cartData = $this->cartService->getCartWithCoupon();
        
        if (empty($cartData['items'])) {
            $_SESSION['flash_error'] = 'Carrinho vazio!';
            $this->redirect('/shop');
            return;
        }

        $this->renderShop('shop/checkout', [
            'title' => 'Finalizar Pedido - Teste Montink',
            'cart' => $cartData
        ]);
    }

    public function getCep(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método não permitido']);
            return;
        }

        $cep = $_POST['cep'] ?? '';
        $result = $this->cepService->getCepData($cep);
        
        $this->json($result);
    }

    public function processOrder(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/shop/cart');
            return;
        }

        $cartData = $this->cartService->getCartWithCoupon();
        
        if (empty($cartData['items'])) {
            $_SESSION['flash_error'] = 'Carrinho vazio!';
            $this->redirect('/shop');
            return;
        }

        $customerData = [
            'name' => $_POST['customer_name'] ?? '',
            'email' => $_POST['customer_email'] ?? '',
            'phone' => $_POST['customer_phone'] ?? '',
            'cep' => $_POST['customer_cep'] ?? '',
            'address' => $_POST['customer_address'] ?? '',
            'city' => $_POST['customer_city'] ?? '',
            'state' => $_POST['customer_state'] ?? ''
        ];

        try {
            $db = $this->container->get('database')->getConnection();
            $db->beginTransaction();

            $stmt = $db->prepare(
                "INSERT INTO sales (customer_name, customer_email, customer_phone, 
                 subtotal, discount, total, payment_method, status, user_id, 
                 shipping_cost, coupon_code, coupon_discount, customer_cep, 
                 customer_address, customer_city, customer_state, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, 'online', 'pending', ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );

            $stmt->execute([
                $customerData['name'],
                $customerData['email'],
                $customerData['phone'],
                $cartData['subtotal'],
                $cartData['coupon_discount'],
                $cartData['total_with_discount'],
                $_SESSION['user_id'] ?? 1,
                $cartData['shipping'],
                $cartData['coupon_code'],
                $cartData['coupon_discount'],
                $customerData['cep'],
                $customerData['address'],
                $customerData['city'],
                $customerData['state']
            ]);

            $saleId = $db->lastInsertId();

            foreach ($cartData['items'] as $item) {
                $stmt = $db->prepare(
                    "INSERT INTO sale_items (sale_id, product_id, quantity, price, total, created_at) 
                     VALUES (?, ?, ?, ?, ?, NOW())"
                );
                $stmt->execute([
                    $saleId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['final_price'],
                    $item['total']
                ]);

                $stmt = $db->prepare(
                    "UPDATE estoque SET quantity = quantity - ? 
                     WHERE product_id = ? AND (variation_id = ? OR variation_id IS NULL)"
                );
                $stmt->execute([
                    $item['quantity'],
                    $item['product_id'],
                    $item['variation_id']
                ]);
            }

            if ($cartData['coupon_code']) {
                $this->couponService->applyCoupon($cartData['coupon_code']);
            }

            $db->commit();

            $stmt = $db->prepare(
                "SELECT si.*, p.name as product_name, p.code as product_code 
                 FROM sale_items si 
                 JOIN products p ON si.product_id = p.id 
                 WHERE si.sale_id = ?"
            );
            $stmt->execute([$saleId]);
            $orderItems = $stmt->fetchAll();

            $stmt = $db->prepare("SELECT * FROM sales WHERE id = ?");
            $stmt->execute([$saleId]);
            $orderData = $stmt->fetch();

            try {
                $this->emailService->sendOrderConfirmation($orderData, $orderItems);
            } catch (\Exception $e) {
                error_log("Erro ao enviar e-mail: " . $e->getMessage());
            }

            $this->cartService->clearCart();

            $_SESSION['flash_success'] = "Pedido #{$saleId} criado com sucesso!";
            $this->redirect('/shop/success/' . $saleId);

        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['flash_error'] = 'Erro ao processar pedido: ' . $e->getMessage();
            $this->redirect('/shop/checkout');
        }
    }

    public function success(string $orderId): void
    {
        $stmt = $this->container->get('database')->getConnection()->prepare(
            "SELECT * FROM sales WHERE id = ?"
        );
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            $_SESSION['flash_error'] = 'Pedido não encontrado!';
            $this->redirect('/shop');
            return;
        }

        $this->renderShop('shop/success', [
            'title' => 'Pedido Finalizado - Teste Montink',
            'order' => $order
        ]);
    }
}