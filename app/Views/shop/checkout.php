<?php
$title = 'Finalizar Pedido - Teste Montink';
use App\Helpers\ViewHelper;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-credit-card"></i> Finalizar Pedido</h1>
    <a href="/shop/cart" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar ao Carrinho
    </a>
</div>

<form method="POST" action="/shop/process-order" id="checkoutForm">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Dados do Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="customer_name" 
                                       name="customer_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="customer_email" 
                                       name="customer_email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="customer_phone" 
                                       name="customer_phone" data-mask="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_cep" class="form-label">CEP *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_cep" 
                                           name="customer_cep" data-mask="cep" required>
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="searchCep()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="customer_address" class="form-label">Endereço *</label>
                                <input type="text" class="form-control" id="customer_address" 
                                       name="customer_address" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="customer_city" class="form-label">Cidade *</label>
                                <input type="text" class="form-control" id="customer_city" 
                                       name="customer_city" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_state" class="form-label">Estado *</label>
                                <input type="text" class="form-control" id="customer_state" 
                                       name="customer_state" maxlength="2" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <h6>Itens (<?= $cart['item_count'] ?>)</h6>
                    <?php foreach ($cart['items'] as $item): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <small>
                            <?= htmlspecialchars($item['display_name']) ?>
                            <br>Qtd: <?= $item['quantity'] ?>
                        </small>
                        <small><?= ViewHelper::formatCurrency($item['total']) ?></small>
                    </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
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
                        <span>Cupom:</span>
                        <span>-<?= ViewHelper::formatCurrency($cart['coupon_discount']) ?></span>
                    </div>
                    <?php endif; ?>

                    <hr>
                    <div class="d-flex justify-content-between h5">
                        <span>Total:</span>
                        <span><?= ViewHelper::formatCurrency($cart['total_with_discount']) ?></span>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Confirmar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function searchCep() {
    const cep = document.getElementById('customer_cep').value;
    const button = event.target;
    
    if (!cep) {
        showError('Digite um CEP para buscar');
        return;
    }

    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch('/shop/get-cep', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cep=' + encodeURIComponent(cep)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('customer_address').value = data.data.logradouro;
            document.getElementById('customer_city').value = data.data.localidade;
            document.getElementById('customer_state').value = data.data.uf;
            showSuccess('CEP encontrado com sucesso!');
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        showError('Erro ao consultar CEP');
        console.error('Error:', error);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-search"></i>';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('customer_cep');
    cepInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 5) {
            value = value.slice(0, 5) + '-' + value.slice(5, 8);
        }
        this.value = value;
    });
});
</script>