<?php
use App\Helpers\ViewHelper;
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-cart"></i> Vendas</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/vendas/create" class="btn btn-primary">
            <i class="bi bi-plus"></i> Nova Venda
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Total</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?= $sale['id'] ?></td>
                        <td><?= ViewHelper::formatDateTime($sale['created_at']) ?></td>
                        <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                        <td><?= htmlspecialchars($sale['user_name']) ?></td>
                        <td><?= ViewHelper::formatCurrency($sale['total']) ?></td>
                        <td><?= ViewHelper::paymentMethodBadge($sale['payment_method']) ?></td>
                        <td>
                            <?php if ($sale['status'] === 'completed'): ?>
                                <span class="badge bg-success">Concluída</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Cancelada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/vendas/view/<?= $sale['id'] ?>" class="btn btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Vendas - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
