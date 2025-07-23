<?php
use App\Helpers\ViewHelper;
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Relatório de Estoque</h1>
    <button class="btn btn-primary" onclick="window.print()">
        <i class="bi bi-printer"></i> Imprimir
    </button>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h6 class="card-title">Total de Produtos</h6>
                <h3 class="mb-0"><?= count($products) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-danger">
            <div class="card-body">
                <h6 class="card-title">Produtos em Falta</h6>
                <h3 class="mb-0"><?= count(array_filter($products, fn($p) => $p['stock'] === 0)) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6 class="card-title">Estoque Baixo</h6>
                <h3 class="mb-0"><?= count($lowStockProducts) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6 class="card-title">Valor Total</h6>
                <h3 class="mb-0">R$ <?= number_format(array_sum(array_map(fn($p) => $p['price'] * $p['stock'], $products)), 2, ',', '.') ?></h3>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($lowStockProducts)): ?>
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Produtos com Estoque Baixo</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Estoque Atual</th>
                        <th>Estoque Mínimo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockProducts as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['code']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td class="text-danger fw-bold"><?= $product['stock'] ?></td>
                        <td><?= $product['min_stock'] ?></td>
                        <td>
                            <a href="/produtos/edit/<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i> Reabastecer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Estoque Completo</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover no-datatable" id="stockTable">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['code']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                        <td>R$ <?= number_format($product['price'], 2, ',', '.') ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td>R$ <?= number_format($product['price'] * $product['stock'], 2, ',', '.') ?></td>
                        <td>
                            <?php if ($product['stock'] === 0): ?>
                                <span class="badge bg-danger">Sem Estoque</span>
                            <?php elseif ($product['stock'] <= $product['min_stock']): ?>
                                <span class="badge bg-warning">Estoque Baixo</span>
                            <?php else: ?>
                                <span class="badge bg-success">Normal</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#stockTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[4, 'asc']], // Ordenar por estoque
        pageLength: 25
    });
});
</script>

<style>
@media print {
    .btn, .card-header {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Relatório de Estoque';
require_once __DIR__ . '/../layout.php';
?>