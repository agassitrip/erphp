<?php
use App\Helpers\ViewHelper;
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-people"></i> Clientes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/clientes/create" class="btn btn-primary">
            <i class="bi bi-plus"></i> Novo Cliente
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
                        <th>CPF/CNPJ</th>
                        <th>Cidade</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= $customer['id'] ?></td>
                        <td><?= htmlspecialchars($customer['name']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= ViewHelper::formatPhone($customer['phone']) ?></td>
                        <td><?= ViewHelper::formatDocument($customer['document']) ?></td>
                        <td><?= htmlspecialchars($customer['city']) ?></td>
                        <td><?= ViewHelper::statusBadge($customer['active']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/clientes/edit/<?= $customer['id'] ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteCustomer(<?= $customer['id'] ?>)">
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
function deleteCustomer(id) {
    confirmDelete('Tem certeza que deseja excluir este cliente?', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/clientes/delete/' + id;
        document.body.appendChild(form);
        form.submit();
    });
}
</script>

<?php
$content = ob_get_clean();
$title = 'Clientes - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
