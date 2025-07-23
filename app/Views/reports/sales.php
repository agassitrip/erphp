<?php
use App\Helpers\ViewHelper;
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Relatório de Vendas</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($salesReport)): ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Total de Vendas</h6>
                    <h3 class="mb-0"><?= $salesReport['total_sales'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h6 class="card-title">Receita Total</h6>
                    <h3 class="mb-0">R$ <?= number_format($salesReport['total_revenue'] ?? 0, 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h6 class="card-title">Ticket Médio</h6>
                    <h3 class="mb-0">R$ <?= number_format($salesReport['average_ticket'] ?? 0, 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h6 class="card-title">Produtos Vendidos</h6>
                    <h3 class="mb-0"><?= $salesReport['total_items'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Vendas por Dia</h5>
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <?php if (!empty($salesReport['top_products'])): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Produtos Mais Vendidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Receita</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salesReport['top_products'] as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= $product['quantity'] ?></td>
                            <td>R$ <?= number_format($product['revenue'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Nenhuma venda encontrada no período selecionado.
    </div>
<?php endif; ?>

<script>
<?php if (!empty($salesReport['daily_sales'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($salesReport['daily_sales'], 'date')) ?>,
            datasets: [{
                label: 'Vendas',
                data: <?= json_encode(array_column($salesReport['daily_sales'], 'total')) ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
});
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
$title = 'Relatório de Vendas';
require_once __DIR__ . '/../layout.php';
?>