<?php
use App\Helpers\ViewHelper;
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-cart"></i> Nova Venda</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/vendas" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0">Dados da Venda</h6>
            </div>
            <div class="card-body">
                <form method="POST" id="saleForm">
                    <?= ViewHelper::csrfField() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Cliente *</label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">Selecione um cliente</option>
                                    <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Forma de Pagamento *</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="cash">Dinheiro</option>
                                    <option value="card">Cartão</option>
                                    <option value="pix">PIX</option>
                                    <option value="transfer">Transferência</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="product_search" class="form-label">Buscar Produto</label>
                        <input type="text" class="form-control" id="product_search" placeholder="Digite o nome ou código do produto">
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0">Itens da Venda</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm" id="sale_items">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Qtd</th>
                                            <th>Preço Unit.</th>
                                            <th>Total</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Desconto</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="discount" name="discount" 
                                           step="0.01" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total da Venda</label>
                                <div class="form-control-plaintext h5" id="total_display">R$ 0,00</div>
                                <input type="hidden" id="subtotal" name="subtotal" value="0">
                                <input type="hidden" id="total" name="total" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/vendas" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Finalizar Venda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let saleItems = [];
let itemCounter = 0;

document.addEventListener('DOMContentLoaded', function() {
    const productSearch = document.getElementById('product_search');
    const discount = document.getElementById('discount');
    
    productSearch.addEventListener('input', function() {
        const term = this.value;
        if (term.length >= 2) {
            console.log('Searching for:', term);
        }
    });

    discount.addEventListener('input', function() {
        updateTotals();
    });
});

function addItem(product) {
    const item = {
        id: ++itemCounter,
        product_id: product.id,
        name: product.name,
        price: product.price,
        quantity: 1,
        total: product.price
    };
    
    saleItems.push(item);
    renderItems();
    updateTotals();
}

function removeItem(itemId) {
    saleItems = saleItems.filter(item => item.id !== itemId);
    renderItems();
    updateTotals();
}

function updateQuantity(itemId, quantity) {
    const item = saleItems.find(item => item.id === itemId);
    if (item) {
        item.quantity = quantity;
        item.total = item.price * quantity;
        renderItems();
        updateTotals();
    }
}

function renderItems() {
    const tbody = document.querySelector('#sale_items tbody');
    tbody.innerHTML = '';
    
    saleItems.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${item.quantity}" min="1" 
                       onchange="updateQuantity(${item.id}, this.value)">
                <input type="hidden" name="items[${item.id}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${item.id}][quantity]" value="${item.quantity}">
                <input type="hidden" name="items[${item.id}][price]" value="${item.price}">
            </td>
            <td>${formatCurrency(item.price)}</td>
            <td>${formatCurrency(item.total)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="removeItem(${item.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateTotals() {
    const subtotal = saleItems.reduce((sum, item) => sum + item.total, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total = subtotal - discount;
    
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
    document.getElementById('total_display').textContent = formatCurrency(total);
}
</script>

<?php
$content = ob_get_clean();
$title = 'Nova Venda - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
