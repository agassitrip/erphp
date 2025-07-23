<?php
use App\Helpers\ViewHelper;
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-code-square display-4 text-primary"></i>
                    <h2 class="mt-3">Teste Montink</h2>
                    <p class="text-muted">Faça login para continuar</p>
                </div>

                <form method="POST" action="/login">
                    <?= ViewHelper::csrfField() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <strong>Usuários de teste:</strong><br>
                        Admin: admin@teste.com / password<br>
                        User: user@teste.com / password
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Login - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
