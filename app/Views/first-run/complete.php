<div class="text-center mb-4">
    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
    <h2 class="mt-3 text-success">Configuração Concluída!</h2>
</div>

<div class="alert alert-success border-success">
    <i class="bi bi-check-circle-fill"></i>
    <strong>Sucesso!</strong> Todas as configurações foram aplicadas corretamente.
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-success h-100">
            <div class="card-body text-center">
                <i class="bi bi-person-check text-success" style="font-size: 2rem;"></i>
                <h5 class="card-title mt-2">Acesso Administrativo</h5>
                <p class="card-text">
                    <strong>Email:</strong> admin@teste.com<br>
                    <strong>Senha:</strong> password
                </p>
                <a href="/login" class="btn btn-success">
                    <i class="bi bi-box-arrow-in-right"></i> Fazer Login Admin
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-primary h-100">
            <div class="card-body text-center">
                <i class="bi bi-shop text-primary" style="font-size: 2rem;"></i>
                <h5 class="card-title mt-2">Loja Virtual</h5>
                <p class="card-text">
                    Explore a loja com produtos de demonstração já configurados.
                </p>
                <a href="/" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Ver Loja
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h5><i class="bi bi-list-check text-success"></i> O que foi configurado:</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li><i class="bi bi-check-circle text-success"></i> Banco de dados criado</li>
                <li><i class="bi bi-check-circle text-success"></i> Tabelas estruturadas</li>
                <li><i class="bi bi-check-circle text-success"></i> Usuários administrativos</li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li><i class="bi bi-check-circle text-success"></i> Produtos de demonstração</li>
                <li><i class="bi bi-check-circle text-success"></i> Configurações do sistema</li>
                <li><i class="bi bi-check-circle text-success"></i> Loja virtual funcional</li>
            </ul>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="bi bi-lightbulb"></i>
    <strong>Próximos passos:</strong><br>
    • Altere as senhas padrão por segurança<br>
    • Configure seus produtos reais<br>
    • Personalize as configurações da loja<br>
    • Faça backup regularmente dos seus dados
</div>

<div class="alert alert-warning">
    <i class="bi bi-shield-exclamation"></i>
    <strong>Importante para produção:</strong><br>
    • Altere as credenciais padrão imediatamente<br>
    • Configure SSL/HTTPS<br>
    • Revise as permissões de arquivo<br>
    • Configure backups automáticos
</div>

<div class="d-grid gap-2">
    <a href="/login" class="btn btn-success btn-lg">
        <i class="bi bi-rocket"></i> Começar a Usar o Sistema
    </a>
    <a href="/" class="btn btn-outline-primary">
        <i class="bi bi-shop"></i> Explorar Loja Virtual
    </a>
</div>


<?php 
$_SESSION['wizard_step'] = 'complete';
unset($_SESSION['wizard_step']);
?>