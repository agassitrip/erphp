<?php
$title = 'Página não encontrada - Teste Montink';
use App\Helpers\MobileComponent;
?>

<!-- Desktop Version -->
<div class="container-fluid d-none d-md-block">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="display-4 text-muted mb-3">404</h1>
                    <h2 class="h4 mb-4">Página não encontrada</h2>
                    
                    <div class="alert alert-info">
                        <strong>O que você estava tentando acessar:</strong><br>
                        <code><?= htmlspecialchars($requestedUri ?? 'Não informado') ?></code>
                    </div>
                    
                    <?php if (isset($ticketId)): ?>
                    <div class="alert alert-secondary">
                        <i class="bi bi-ticket-perforated"></i>
                        <strong>Ticket de erro:</strong> <?= htmlspecialchars($ticketId) ?>
                        <br><small class="text-muted">Este código pode ser usado pelos administradores para investigar o problema.</small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5><i class="bi bi-compass"></i> Páginas disponíveis:</h5>
                            <div class="list-group list-group-flush text-start">
                                <a href="/" class="list-group-item list-group-item-action">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                                <a href="/produtos" class="list-group-item list-group-item-action">
                                    <i class="bi bi-box"></i> Produtos
                                </a>
                                <a href="/clientes" class="list-group-item list-group-item-action">
                                    <i class="bi bi-people"></i> Clientes
                                </a>
                                <a href="/fornecedores" class="list-group-item list-group-item-action">
                                    <i class="bi bi-truck"></i> Fornecedores
                                </a>
                                <a href="/vendas" class="list-group-item list-group-item-action">
                                    <i class="bi bi-cart"></i> Vendas
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5><i class="bi bi-graph-up"></i> Relatórios:</h5>
                            <div class="list-group list-group-flush text-start">
                                <a href="/relatorios/vendas" class="list-group-item list-group-item-action">
                                    <i class="bi bi-bar-chart"></i> Relatório de Vendas
                                </a>
                                <a href="/relatorios/estoque" class="list-group-item list-group-item-action">
                                    <i class="bi bi-boxes"></i> Relatório de Estoque
                                </a>
                                <a href="/relatorios/financeiro" class="list-group-item list-group-item-action">
                                    <i class="bi bi-cash-stack"></i> Relatório Financeiro
                                </a>
                            </div>
                            
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <div class="mt-3">
                                <h6><i class="bi bi-gear"></i> Administração:</h6>
                                <div class="list-group list-group-flush text-start">
                                    <a href="/usuarios" class="list-group-item list-group-item-action">
                                        <i class="bi bi-person-gear"></i> Usuários
                                    </a>
                                    <a href="/admin/404-logs" class="list-group-item list-group-item-action">
                                        <i class="bi bi-bug"></i> Logs de Erro 404
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
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

<!-- Mobile Version -->
<div class="d-block d-md-none px-3">
    <div class="mobile-card">
        <div class="mobile-card-header text-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>404 - Página não encontrada
        </div>
        <div class="mobile-card-body text-center">
            <div class="mb-4">
                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
            </div>
            
            <?= MobileComponent::mobileAlert(
                'O que você estava tentando acessar: ' . htmlspecialchars($requestedUri ?? 'Não informado'), 
                'info'
            ) ?>
            
            <?php if (isset($ticketId)): ?>
            <?= MobileComponent::mobileAlert(
                'Ticket de erro: ' . htmlspecialchars($ticketId) . '. Este código pode ser usado pelos administradores.', 
                'warning'
            ) ?>
            <?php endif; ?>
            
            <div class="d-grid gap-2 mt-4">
                <a href="/shop" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Ir para a Loja
                </a>
                <a href="/" class="btn btn-outline-primary">
                    <i class="bi bi-house"></i> Dashboard
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Quick Links -->
    <div class="mobile-card mt-3">
        <div class="mobile-card-body">
            <h6 class="text-center mb-3">
                <i class="bi bi-compass"></i> Páginas disponíveis
            </h6>
            <div class="row">
                <div class="col-6">
                    <a href="/shop" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-shop"></i><br><small>Loja</small>
                    </a>
                    <a href="/produtos" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-box"></i><br><small>Produtos</small>
                    </a>
                    <a href="/clientes" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-people"></i><br><small>Clientes</small>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/vendas" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-cart"></i><br><small>Vendas</small>
                    </a>
                    <a href="/fornecedores" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-truck"></i><br><small>Fornecedores</small>
                    </a>
                    <a href="/relatorios/vendas" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-graph-up"></i><br><small>Relatórios</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>