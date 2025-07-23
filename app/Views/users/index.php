<?php
use App\Helpers\ViewHelper;
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-person-gear"></i> Usuários</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/usuarios/create" class="btn btn-primary">
            <i class="bi bi-plus"></i> Novo Usuário
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
                        <th>Perfil</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-danger">Administrador</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Usuário</span>
                            <?php endif; ?>
                        </td>
                        <td><?= ViewHelper::statusBadge($user['active']) ?></td>
                        <td><?= ViewHelper::formatDate($user['created_at']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/usuarios/edit/<?= $user['id'] ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
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
function deleteUser(id) {
    confirmDelete('Tem certeza que deseja excluir este usuário?', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/usuarios/delete/' + id;
        document.body.appendChild(form);
        form.submit();
    });
}
</script>

<?php
$content = ob_get_clean();
$title = 'Usuários - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
