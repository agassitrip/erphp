<?php
use App\Helpers\ViewHelper;
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-truck"></i> Fornecedores</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/fornecedores/create" class="btn btn-primary">
            <i class="bi bi-plus"></i> Novo Fornecedor
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
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>CNPJ</th>
                        <th>Cidade</th>
                        <th>Produtos</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?= $supplier['id'] ?></td>
                        <td><?= htmlspecialchars($supplier['name']) ?></td>
                        <td><?= htmlspecialchars($supplier['email']) ?></td>
                        <td><?= ViewHelper::formatPhone($supplier['phone']) ?></td>
                        <td><?= ViewHelper::formatDocument($supplier['document']) ?></td>
                        <td><?= htmlspecialchars($supplier['city']) ?></td>
                        <td><span class="badge bg-info"><?= $supplier['product_count'] ?? 0 ?></span></td>
                        <td><?= ViewHelper::statusBadge($supplier['active']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/fornecedores/edit/<?= $supplier['id'] ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteSupplier(<?= $supplier['id'] ?>)">
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

<script>
function deleteSupplier(id) {
    confirmDelete('Tem certeza que deseja excluir este fornecedor?', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/fornecedores/delete/' + id;
        document.body.appendChild(form);
        form.submit();
    });
}
</script>

<?php
$content = ob_get_clean();
$title = 'Fornecedores - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
