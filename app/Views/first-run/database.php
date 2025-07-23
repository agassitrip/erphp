<div class="text-center mb-4">
    <i class="bi bi-database text-primary" style="font-size: 3rem;"></i>
    <h3 class="mt-3">Configuração do Banco de Dados</h3>
    <p class="text-muted">Configure a conexão com seu banco MySQL</p>
</div>

<form method="POST" action="/first-run/database">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="mb-3">
                <label for="db_host" class="form-label">
                    <i class="bi bi-server"></i> Servidor
                </label>
                <input type="text" class="form-control" id="db_host" name="db_host" 
                       value="<?= htmlspecialchars($_SESSION['old_input']['db_host'] ?? 'localhost') ?>" 
                       placeholder="localhost" required>
            </div>

            <div class="mb-3">
                <label for="db_port" class="form-label">
                    <i class="bi bi-ethernet"></i> Porta
                </label>
                <input type="number" class="form-control" id="db_port" name="db_port" 
                       value="<?= htmlspecialchars($_SESSION['old_input']['db_port'] ?? '3306') ?>" 
                       placeholder="3306" min="1" max="65535" required>
            </div>

            <div class="mb-3">
                <label for="db_database" class="form-label">
                    <i class="bi bi-database"></i> Nome do Banco
                </label>
                <input type="text" class="form-control" id="db_database" name="db_database" 
                       value="<?= htmlspecialchars($_SESSION['old_input']['db_database'] ?? 'erp_system') ?>" 
                       placeholder="erp_system" required>
            </div>

            <div class="mb-3">
                <label for="db_username" class="form-label">
                    <i class="bi bi-person"></i> Usuário
                </label>
                <input type="text" class="form-control" id="db_username" name="db_username" 
                       value="<?= htmlspecialchars($_SESSION['old_input']['db_username'] ?? '') ?>" 
                       placeholder="root" required>
            </div>

            <div class="mb-4">
                <label for="db_password" class="form-label">
                    <i class="bi bi-lock"></i> Senha
                </label>
                <input type="password" class="form-control" id="db_password" name="db_password" 
                       placeholder="Digite a senha do banco">
            </div>


            <div class="row">
                <div class="col-6">
                    <a href="/first-run" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Testar Conexão
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php 
$_SESSION['wizard_step'] = 'database';
if (isset($_SESSION['old_input'])) {
    unset($_SESSION['old_input']);
}
?>