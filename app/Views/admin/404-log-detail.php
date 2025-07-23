<?php
$title = 'Detalhes do Log 404 - Teste Montink';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-file-text"></i> Detalhes do Log 404</h1>
    <a href="/admin/404-logs" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar para lista
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> Informações da Requisição</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Ticket ID:</dt>
                    <dd class="col-sm-9">
                        <code><?= htmlspecialchars($log['ticket_id']) ?></code>
                    </dd>
                    
                    <dt class="col-sm-3">Data/Hora:</dt>
                    <dd class="col-sm-9">
                        <?= date('d/m/Y H:i:s', strtotime($log['timestamp'])) ?>
                        <small class="text-muted">(<?= $log['timestamp'] ?>)</small>
                    </dd>
                    
                    <dt class="col-sm-3">URI Solicitada:</dt>
                    <dd class="col-sm-9">
                        <code class="bg-light p-2 d-block"><?= htmlspecialchars($log['uri']) ?></code>
                    </dd>
                    
                    <dt class="col-sm-3">Método HTTP:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-<?= $log['method'] === 'GET' ? 'primary' : 'warning' ?>">
                            <?= htmlspecialchars($log['method']) ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-3">Endereço IP:</dt>
                    <dd class="col-sm-9">
                        <code><?= htmlspecialchars($log['ip']) ?></code>
                    </dd>
                    
                    <dt class="col-sm-3">User Agent:</dt>
                    <dd class="col-sm-9">
                        <small class="text-muted"><?= htmlspecialchars($log['user_agent']) ?></small>
                    </dd>
                    
                    <dt class="col-sm-3">Referer:</dt>
                    <dd class="col-sm-9">
                        <?php if ($log['referer']): ?>
                            <code><?= htmlspecialchars($log['referer']) ?></code>
                        <?php else: ?>
                            <span class="text-muted">Não informado</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
        
        <?php if (!empty($log['context'])): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-code-square"></i> Contexto Adicional</h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded"><code><?= htmlspecialchars(json_encode($log['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></code></pre>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-person"></i> Informações do Usuário</h5>
            </div>
            <div class="card-body">
                <?php if ($log['user_id']): ?>
                    <dl>
                        <dt>Nome:</dt>
                        <dd><?= htmlspecialchars($log['user_name']) ?></dd>
                        
                        <dt>ID do Usuário:</dt>
                        <dd><code><?= $log['user_id'] ?></code></dd>
                        
                        <dt>Status:</dt>
                        <dd>
                            <span class="badge bg-success">
                                <i class="bi bi-person-check"></i> Autenticado
                            </span>
                        </dd>
                    </dl>
                <?php else: ?>
                    <div class="text-center">
                        <i class="bi bi-person-x text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Usuário não autenticado</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-compass"></i> Rotas Disponíveis</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="routesAccordion">
                    <?php foreach ($log['available_routes'] as $section => $routes): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse<?= md5($section) ?>">
                                <?= htmlspecialchars($section) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= md5($section) ?>" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <?php if (is_array($routes)): ?>
                                    <?php foreach ($routes as $name => $route): ?>
                                    <div class="mb-1">
                                        <small>
                                            <strong><?= htmlspecialchars($name) ?>:</strong>
                                            <code><?= htmlspecialchars($route) ?></code>
                                        </small>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div>
                                        <code><?= htmlspecialchars($routes) ?></code>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>