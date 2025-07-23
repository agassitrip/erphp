<div class="text-center mb-4">
    <i class="bi bi-code-square text-primary" style="font-size: 4rem;"></i>
    <h2 class="mt-3">Bem-vindo ao Teste Montink</h2>
    <p class="text-muted">Configuração rápida para testes</p>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    <strong>Primeira execução detectada!</strong><br>
    Vou te ajudar a executar esse sistema rapidinho.
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-primary mb-3">
            <div class="card-body text-center">
                <i class="bi bi-database text-primary" style="font-size: 2rem;"></i>
                <h5 class="card-title mt-2">Configuração</h5>
                <p class="card-text small">Configurar conexão com banco de dados</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success mb-3">
            <div class="card-body text-center">
                <i class="bi bi-cloud-download text-success" style="font-size: 2rem;"></i>
                <h5 class="card-title mt-2">Importação</h5>
                <p class="card-text small">Importar dados padrão ou backup</p>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h5><i class="bi bi-check-circle-fill text-success"></i> O que será configurado:</h5>
    <ul class="list-unstyled ms-4">
        <li><i class="bi bi-check text-success"></i> Conexão com banco de dados MySQL</li>
        <li><i class="bi bi-check text-success"></i> Criação das tabelas necessárias</li>
        <li><i class="bi bi-check text-success"></i> Usuários administrativos padrão</li>
        <li><i class="bi bi-check text-success"></i> Produtos de demonstração</li>
        <li><i class="bi bi-check text-success"></i> Configurações do sistema</li>
    </ul>
</div>

<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>Requisitos:</strong><br>
    • MySQL 5.7+ ou MariaDB 10.3+<br>
    • PHP 8.0+ com extensão PDO<br>
    • Permissões de escrita na pasta do projeto
</div>

<div class="d-grid">
    <a href="/first-run/database" class="btn btn-primary btn-lg">
        <i class="bi bi-arrow-right"></i> Começar Configuração
    </a>
</div>

<div class="text-center mt-3">
    <small class="text-muted">
        <i class="bi bi-shield-check"></i>
        Seus dados estarão seguros durante todo o processo (você já sabe)
    </small>
</div>

<?php $_SESSION['wizard_step'] = 'welcome'; ?>