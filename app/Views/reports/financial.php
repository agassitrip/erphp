<?php
use App\Helpers\ViewHelper;
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Relatório Financeiro</h1>
    <div>
        <button class="btn btn-success" onclick="exportToExcel()">
            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Receita Bruta</h6>
                    <h3 class="text-success mb-0">R$ <?= number_format($salesReport['total_revenue'] ?? 0, 2, ',', '.') ?></h3>
                    <small class="text-muted"><?= $salesReport['total_sales'] ?? 0 ?> vendas realizadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Custo dos Produtos</h6>
                    <h3 class="text-danger mb-0">R$ <?= number_format($salesReport['total_cost'] ?? 0, 2, ',', '.') ?></h3>
                    <small class="text-muted">Custo médio: <?= number_format($salesReport['average_cost'] ?? 0, 2, ',', '.') ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Lucro Bruto</h6>
                    <h3 class="text-primary mb-0">R$ <?= number_format(($salesReport['total_revenue'] ?? 0) - ($salesReport['total_cost'] ?? 0), 2, ',', '.') ?></h3>
                    <small class="text-muted">Margem: <?= number_format($salesReport['profit_margin'] ?? 0, 1) ?>%</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Fluxo de Caixa</h5>
                </div>
                <div class="card-body">
                    <canvas id="cashFlowChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Distribuição de Receita</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Análise por Categoria</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover no-datatable" id="categoryTable">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Quantidade Vendida</th>
                            <th>Receita</th>
                            <th>Custo</th>
                            <th>Lucro</th>
                            <th>Margem %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($salesReport['category_analysis'])): ?>
                            <?php foreach ($salesReport['category_analysis'] as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= $category['quantity'] ?></td>
                                <td>R$ <?= number_format($category['revenue'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($category['cost'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($category['profit'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $category['margin'] >= 30 ? 'success' : ($category['margin'] >= 20 ? 'warning' : 'danger') ?>">
                                        <?= number_format($category['margin'], 1) ?>%
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Demonstrativo Diário</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm no-datatable" id="dailyTable">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Vendas</th>
                            <th>Receita</th>
                            <th>Custo</th>
                            <th>Lucro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($salesReport['daily_sales'])): ?>
                            <?php foreach ($salesReport['daily_sales'] as $day): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($day['date'])) ?></td>
                                <td><?= $day['count'] ?></td>
                                <td>R$ <?= number_format($day['total'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($day['cost'] ?? 0, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($day['profit'] ?? 0, 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Nenhum dado financeiro encontrado no período selecionado.
    </div>
<?php endif; ?>

<script>
<?php if (!empty($salesReport)): ?>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($salesReport['daily_sales'])): ?>
    const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
    new Chart(cashFlowCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_map(fn($d) => date('d/m', strtotime($d['date'])), $salesReport['daily_sales'])) ?>,
            datasets: [{
                label: 'Receita',
                data: <?= json_encode(array_column($salesReport['daily_sales'], 'total')) ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Lucro',
                data: <?= json_encode(array_column($salesReport['daily_sales'], 'profit')) ?>,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
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
    <?php endif; ?>

    <?php if (!empty($salesReport['category_analysis'])): ?>
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($salesReport['category_analysis'], 'name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($salesReport['category_analysis'], 'revenue')) ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    <?php endif; ?>

    $('#categoryTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[2, 'desc']]
    });

    $('#dailyTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        order: [[0, 'desc']]
    });
});

function exportToExcel() {
    const table = document.getElementById('categoryTable');
    const wb = XLSX.utils.table_to_book(table);
    XLSX.writeFile(wb, 'relatorio_financeiro_' + new Date().toISOString().slice(0,10) + '.xlsx');
}
<?php endif; ?>
</script>

<style>
@media print {
    .btn, .card {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .card {
        break-inside: avoid;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Relatório Financeiro';
require_once __DIR__ . '/../layout.php';
?>