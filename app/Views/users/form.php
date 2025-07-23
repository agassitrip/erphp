<?php
use App\Helpers\ViewHelper;
ob_start();
$isEdit = isset($user);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-gear"></i> 
        <?= $isEdit ? 'Editar Usuário' : 'Novo Usuário' ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/usuarios" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST">
                    <?= ViewHelper::csrfField() ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Senha <?= $isEdit ? '(deixe em branco para manter a atual)' : '*' ?>
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               <?= !$isEdit ? 'required' : '' ?>>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Perfil *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" <?= ($user['role'] ?? '') == 'user' ? 'selected' : '' ?>>Usuário</option>
                            <option value="admin" <?= ($user['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/usuarios" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> <?= $isEdit ? 'Atualizar' : 'Salvar' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = ($isEdit ? 'Editar' : 'Novo') . ' Usuário - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
