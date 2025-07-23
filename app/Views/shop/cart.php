<?php
$title = 'Carrinho - Teste Montink';
use App\Helpers\ViewHelper;
use App\Helpers\MobileComponent;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-cart"></i> Carrinho de Compras</h1>
    <a href="/shop" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Continuar Comprando
    </a>
</div>

<?php if (empty($cart['items'])): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    <strong>Seu carrinho está vazio.</strong>
    <br>
    <a href="/shop" class="btn btn-primary mt-2">
        <i class="bi bi-shop"></i> Ir às Compras
    </a>
</div>
<?php else: ?>
<div class="d-none d-md-block">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Itens do Carrinho</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cart['items'] as $key => $item): ?>
                    <div class="row align-items-center border-bottom py-3">
                        <div class="col-md-6">
                            <h6 class="mb-1"><?= htmlspecialchars($item['display_name']) ?></h6>
                            <small class="text-muted">
                                Código: <?= htmlspecialchars($item['code']) ?>
                                <br>Preço unitário: <?= ViewHelper::formatCurrency($item['final_price']) ?>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="/shop/update-cart" class="d-flex">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="key" value="<?= $key ?>">
                                <input type="number" class="form-control form-control-sm me-2" 
                                       name="quantity" value="<?= $item['quantity'] ?>" 
                                       min="1" max="<?= ($item['available_stock'] ?? 0) + $item['quantity'] ?>"
                                       onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="col-md-2">
                            <strong><?= ViewHelper::formatCurrency($item['total']) ?></strong>
                        </div>
                        <div class="col-md-1">
                            <form method="POST" action="/shop/update-cart">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="key" value="<?= $key ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span><?= ViewHelper::formatCurrency($cart['subtotal']) ?></span>
                </div>
                
                <div class="d-flex justify-content-between">
                    <span>Frete:</span>
                    <span>
                        <?php if ($cart['shipping'] == 0): ?>
                            <span class="text-success">Grátis</span>
                        <?php else: ?>
                            <?= ViewHelper::formatCurrency($cart['shipping']) ?>
                        <?php endif; ?>
                    </span>
                </div>

                <?php if (isset($cart['coupon_code'])): ?>
                <div class="d-flex justify-content-between text-success">
                    <span>Cupom (<?= htmlspecialchars($cart['coupon_code']) ?>):</span>
                    <span>-<?= ViewHelper::formatCurrency($cart['coupon_discount']) ?></span>
                </div>
                <div class="text-end">
                    <a href="/shop/remove-coupon" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x"></i> Remover Cupom
                    </a>
                </div>
                <?php endif; ?>

                <hr>
                <div class="d-flex justify-content-between h5">
                    <span>Total:</span>
                    <span><?= ViewHelper::formatCurrency($cart['total_with_discount']) ?></span>
                </div>

                <?php if (!isset($cart['coupon_code'])): ?>
                <div class="mt-3">
                    <h6>Cupom de Desconto</h6>
                    <form method="POST" action="/shop/apply-coupon">
                        <div class="input-group">
                            <input type="text" class="form-control" name="coupon_code" 
                                   placeholder="Digite o cupom" required>
                            <button type="submit" class="btn btn-outline-primary">
                                Aplicar
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <div class="d-grid gap-2 mt-4">
                    <a href="/shop/checkout" class="btn btn-success">
                        <i class="bi bi-credit-card"></i> Finalizar Pedido
                    </a>
                    <a href="/shop" class="btn btn-outline-primary">
                        <i class="bi bi-plus"></i> Adicionar Mais Itens
                    </a>
                </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="bi bi-truck"></i> Informações de Frete</h6>
                    <small class="text-muted">
                        • Acima de R$ 200,00: <strong>Frete Grátis</strong><br>
                        • De R$ 52,00 a R$ 166,59: <strong>R$ 15,00</strong><br>
                        • Outros valores: <strong>R$ 20,00</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Version -->
<div class="d-block d-md-none">
    <div class="px-2">
        <?php foreach ($cart['items'] as $key => $item): ?>
            <?php 
            $mobileItem = [
                'key' => $key,
                'name' => $item['display_name'],
                'final_price' => $item['final_price'],
                'total' => $item['total'],
                'quantity' => $item['quantity'],
                'stock_quantity' => $item['stock_quantity'],
                'available_stock' => $item['available_stock'] ?? 0
            ];
            echo MobileComponent::mobileCartItem($mobileItem);
            ?>
        <?php endforeach; ?>
        
        <!-- Mobile Summary Card -->
        <div class="mobile-card mt-3">
            <div class="mobile-card-header">
                <i class="bi bi-receipt"></i> Resumo do Pedido
            </div>
            <div class="mobile-card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span><?= ViewHelper::formatCurrency($cart['subtotal']) ?></span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <span>
                        <?php if ($cart['shipping'] == 0): ?>
                            <span class="text-success">Grátis</span>
                        <?php else: ?>
                            <?= ViewHelper::formatCurrency($cart['shipping']) ?>
                        <?php endif; ?>
                    </span>
                </div>

                <?php if (isset($cart['coupon_code'])): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Cupom (<?= htmlspecialchars($cart['coupon_code']) ?>):</span>
                    <span>-<?= ViewHelper::formatCurrency($cart['coupon_discount']) ?></span>
                </div>
                <div class="text-end mb-2">
                    <a href="/shop/remove-coupon" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x"></i> Remover
                    </a>
                </div>
                <?php endif; ?>

                <hr>
                <div class="d-flex justify-content-between h5 mb-3">
                    <span>Total:</span>
                    <span><?= ViewHelper::formatCurrency($cart['total_with_discount']) ?></span>
                </div>

                <?php if (!isset($cart['coupon_code'])): ?>
                <div class="mb-3">
                    <form method="POST" action="/shop/apply-coupon">
                        <div class="input-group">
                            <input type="text" class="form-control" name="coupon_code" 
                                   placeholder="Cupom de desconto" required>
                            <button type="submit" class="btn btn-outline-primary">
                                Aplicar
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <a href="/shop/checkout" class="btn btn-success">
                        <i class="bi bi-credit-card"></i> Finalizar Pedido
                    </a>
                    <a href="/shop" class="btn btn-outline-primary">
                        <i class="bi bi-plus"></i> Continuar Comprando
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Mobile Shipping Info -->
        <div class="mobile-card mt-3">
            <div class="mobile-card-body">
                <h6><i class="bi bi-truck"></i> Informações de Frete</h6>
                <small class="text-muted">
                    • Acima de R$ 200: <strong>Frete Grátis</strong><br>
                    • De R$ 52 a R$ 166: <strong>R$ 15,00</strong><br>
                    • Outros valores: <strong>R$ 20,00</strong>
                </small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>