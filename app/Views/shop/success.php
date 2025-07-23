<?php
$title = 'Pedido Finalizado - Teste Montink';
use App\Helpers\ViewHelper;
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                
                <h1 class="display-6 text-success mb-3">Pedido Confirmado!</h1>
                <h2 class="h4 mb-4">Pedido #<?= $order['id'] ?></h2>
                
                <div class="alert alert-success">
                    <strong>Seu pedido foi processado com sucesso!</strong><br>
                    Em breve você receberá um e-mail de confirmação com todos os detalhes.
                </div>
                
                <div class="row text-start">
                    <div class="col-md-6">
                        <h5><i class="bi bi-person"></i> Dados do Cliente</h5>
                        <p class="mb-1"><strong>Nome:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p class="mb-1"><strong>E-mail:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                        <?php if ($order['customer_phone']): ?>
                        <p class="mb-1"><strong>Telefone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <h5><i class="bi bi-truck"></i> Entrega</h5>
                        <?php if ($order['customer_address']): ?>
                        <p class="mb-1">
                            <?= htmlspecialchars($order['customer_address']) ?><br>
                            <?= htmlspecialchars($order['customer_city']) ?> - <?= htmlspecialchars($order['customer_state']) ?><br>
                            CEP: <?= htmlspecialchars($order['customer_cep']) ?>
                        </p>
                        <?php endif; ?>
                        <p class="mb-1">
                            <strong>Frete:</strong> 
                            <?php if ($order['shipping_cost'] == 0): ?>
                                <span class="text-success">Grátis</span>
                            <?php else: ?>
                                <?= ViewHelper::formatCurrency($order['shipping_cost']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <div class="row text-start mt-4">
                    <div class="col-md-8">
                        <h5><i class="bi bi-receipt"></i> Resumo Financeiro</h5>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span><?= ViewHelper::formatCurrency($order['subtotal']) ?></span>
                        </div>
                        
                        <?php if ($order['coupon_code']): ?>
                        <div class="d-flex justify-content-between text-success">
                            <span>Cupom (<?= htmlspecialchars($order['coupon_code']) ?>):</span>
                            <span>-<?= ViewHelper::formatCurrency($order['coupon_discount']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between">
                            <span>Frete:</span>
                            <span>
                                <?php if ($order['shipping_cost'] == 0): ?>
                                    <span class="text-success">Grátis</span>
                                <?php else: ?>
                                    <?= ViewHelper::formatCurrency($order['shipping_cost']) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between h5">
                            <span>Total Pago:</span>
                            <span class="text-success"><?= ViewHelper::formatCurrency($order['total']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="/shop" class="btn btn-primary me-2">
                        <i class="bi bi-shop"></i> Continuar Comprando
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="bi bi-printer"></i> Imprimir Pedido
                    </button>
                </div>
                
                <div class="mt-4 text-muted">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        Em caso de dúvidas, entre em contato conosco informando o número do pedido.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>