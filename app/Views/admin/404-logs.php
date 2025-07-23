<?php
$title = 'Logs de Erro 404 - Teste Montink';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-bug"></i> Logs de Erro 404</h1>
    <div>
        <span class="badge bg-info">Total: <?= count($logs) ?> registros</span>
    </div>
</div>

<?php if (empty($logs)): ?>
<div class="alert alert-success">
    <i class="bi bi-check-circle"></i>
    <strong>Excelente!</strong> Nenhum erro 404 foi registrado.
</div>
<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>URI Solicitada</th>
                        <th>Método</th>
                        <th>Usuário</th>
                        <th>IP</th>
                        <th>Ticket</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i:s', strtotime($log['timestamp'])) ?>
                            </small>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($log['uri']) ?></code>
                        </td>
                        <td>
                            <span class="badge bg-<?= $log['method'] === 'GET' ? 'primary' : 'warning' ?>">
                                <?= htmlspecialchars($log['method']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($log['user_name']): ?>
                                <i class="bi bi-person-check"></i>
                                <?= htmlspecialchars($log['user_name']) ?>
                                <small class="text-muted">(ID: <?= $log['user_id'] ?>)</small>
                            <?php else: ?>
                                <span class="text-muted">
                                    <i class="bi bi-person-x"></i> Não autenticado
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="font-monospace"><?= htmlspecialchars($log['ip']) ?></small>
                        </td>
                        <td>
                            <code class="small"><?= substr($log['ticket_id'], 0, 12) ?>...</code>
                        </td>
                        <td>
                            <a href="/admin/404-logs/<?= urlencode($log['ticket_id']) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver detalhes
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-bar-chart"></i> URIs mais acessadas (404)</h6>
                </div>
                <div class="card-body">
                    <?php
                    $uriCounts = [];
                    foreach ($logs as $log) {
                        $uri = $log['uri'];
                        $uriCounts[$uri] = ($uriCounts[$uri] ?? 0) + 1;
                    }
                    arsort($uriCounts);
                    $topUris = array_slice($uriCounts, 0, 5, true);
                    ?>
                    
                    <?php if (empty($topUris)): ?>
                        <p class="text-muted">Nenhum dado disponível</p>
                    <?php else: ?>
                        <?php foreach ($topUris as $uri => $count): ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <code class="small"><?= htmlspecialchars($uri) ?></code>
                                <span class="badge bg-danger"><?= $count ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-clock"></i> Últimos 7 dias</h6>
                </div>
                <div class="card-body">
                    <?php
                    $last7Days = [];
                    $now = time();
                    for ($i = 6; $i >= 0; $i--) {
                        $date = date('Y-m-d', $now - ($i * 24 * 60 * 60));
                        $last7Days[$date] = 0;
                    }
                    
                    foreach ($logs as $log) {
                        $logDate = date('Y-m-d', strtotime($log['timestamp']));
                        if (isset($last7Days[$logDate])) {
                            $last7Days[$logDate]++;
                        }
                    }
                    ?>
                    
                    <?php foreach ($last7Days as $date => $count): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span><?= date('d/m/Y', strtotime($date)) ?></span>
                            <span class="badge bg-<?= $count > 0 ? 'danger' : 'success' ?>"><?= $count ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>