<?php
$title = 'Loja - Teste Montink';
use App\Helpers\ViewHelper;
use App\Helpers\MobileComponent;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-shop"></i> Loja Online</h1>
    <a href="/shop/cart" class="btn btn-primary">
        <i class="bi bi-cart"></i> Ver Carrinho
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <span class="badge bg-warning text-dark"><?= count($_SESSION['cart']) ?></span>
        <?php endif; ?>
    </a>
</div>

<?php if (isset($needs_migration) && $needs_migration): ?>
<div class="alert alert-warning alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>Atenção!</strong> Para funcionalidade completa da loja (cupons, controle de estoque avançado), 
    execute as migrações do banco de dados.
    <a href="/migrate/status" class="btn btn-sm btn-outline-warning ms-2">
        <i class="bi bi-database-gear"></i> Verificar Migrações
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!$is_logged_in && $total_products > 6): ?>
<div class="alert alert-info alert-dismissible fade show">
    <div class="row align-items-center">
        <div class="col-md-8">
            <i class="bi bi-info-circle"></i>
            <strong>Mostrando apenas <?= count($products) ?> de <?= $total_products ?> produtos disponíveis.</strong>
            <br>
            <small>Cadastre-se para ver todos os produtos e aproveitar nossos benefícios exclusivos!</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="/register" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Cadastre-se Grátis
            </a>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (empty($products)): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    <strong>Nenhum produto disponível no momento.</strong>
</div>
<?php else: ?>
<div class="d-none d-md-block">
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text">
                        <small class="text-muted">Código: <?= htmlspecialchars($product['code']) ?></small>
                    </p>
                    <p class="card-text flex-grow-1">
                        <?= htmlspecialchars($product['description'] ?? '') ?>
                    </p>
                    
                    <div class="mb-3">
                        <span class="h5 text-primary"><?= ViewHelper::formatCurrency($product['price']) ?></span>
                        <br>
                        <small class="text-muted">
                            <i class="bi bi-box"></i> 
                            Estoque: <?= $product['available_stock'] ?? 0 ?> unidades
                            <?php if (($product['stock_quantity'] ?? 0) > ($product['available_stock'] ?? 0)): ?>
                                <br><span class="text-warning"><i class="bi bi-cart"></i> <?= ($product['stock_quantity'] ?? 0) - ($product['available_stock'] ?? 0) ?> no carrinho</span>
                            <?php endif; ?>
                        </small>
                    </div>

                    <form method="POST" action="/shop/add-to-cart" class="mt-auto">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <input type="number" class="form-control" name="quantity" 
                                       value="1" min="1" max="<?= $product['available_stock'] ?? 0 ?>" required>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-success w-100" 
                                        <?= ($product['available_stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php if (($product['available_stock'] ?? 0) <= 0): ?>
                            <small class="text-danger">
                                <i class="bi bi-exclamation-triangle"></i> Sem estoque disponível
                            </small>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="mobile-product-grid d-block d-md-none">
    <?php foreach ($products as $product): ?>
        <?= MobileComponent::mobileProductCard($product) ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>