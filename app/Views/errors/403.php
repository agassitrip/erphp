<?php
$title = 'Acesso Negado - Teste Montink';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-shield-exclamation text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="display-4 text-muted mb-3">403</h1>
                    <h2 class="h4 mb-4">Acesso Negado</h2>
                    
                    <div class="alert alert-danger">
                        <strong>Você não tem permissão para acessar esta página.</strong><br>
                        Esta área é restrita a administradores do sistema.
                    </div>
                    
                    <div class="mt-4">
                        <a href="/" class="btn btn-primary me-2">
                            <i class="bi bi-house"></i> Ir para o Dashboard
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>