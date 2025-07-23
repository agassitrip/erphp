<?php
use App\Helpers\ViewHelper;
use App\Helpers\MobileComponent;
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-box"></i> Produtos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/produtos/create" class="btn btn-primary">
            <i class="bi bi-plus"></i> Novo Produto
        </a>
    </div>
</div>

<!-- Desktop Table -->
<div class="card shadow d-none d-md-block">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Código</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th>Categoria</th>
                        <th>Fornecedor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['code']) ?></td>
                        <td><?= ViewHelper::formatCurrency($product['price']) ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td><?= ViewHelper::stockAlert($product['stock'], $product['min_stock']) ?></td>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($product['supplier_name'] ?? 'N/A') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/produtos/edit/<?= $product['id'] ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteProduct(<?= $product['id'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mobile Cards -->
<?php 
$mobileConfig = [
    'title_field' => 'name',
    'subtitle_field' => 'code',
    'extra_fields' => [
        'formatted_price' => 'Preço',
        'stock' => 'Estoque',
        'category_name' => 'Categoria'
    ],
    'actions' => [
        ['url' => '/produtos/edit/{id}', 'icon' => 'bi-pencil', 'type' => 'primary'],
        ['url' => 'javascript:deleteProduct({id})', 'icon' => 'bi-trash', 'type' => 'danger']
    ]
];

$mobileProducts = array_map(function($product) {
    $product['formatted_price'] = ViewHelper::formatCurrency((float)$product['price']);
    $product['stock'] = $product['stock'] . ' un.';
    return $product;
}, $products);

echo MobileComponent::mobileTableCard($mobileProducts, $mobileConfig);
?>

<?= MobileComponent::mobileFab('/produtos/create', 'bi-plus', 'Novo Produto') ?>

<script>
function deleteProduct(id) {
    confirmDelete('Tem certeza que deseja excluir este produto?', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/produtos/delete/' + id;
        document.body.appendChild(form);
        form.submit();
    });
}
</script>

<?php
$content = ob_get_clean();
$title = 'Produtos - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
