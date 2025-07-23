<?php
$title = 'Sessões Ativas - Teste Montink';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-people"></i> Monitoramento de Sessões</h1>
    <div>
        <button class="btn btn-outline-primary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Atualizar
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-circle-fill me-2"></i>
                    <div>
                        <h5 class="card-title mb-0"><?= $report['online_users'] ?></h5>
                        <small>Online agora</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-check me-2"></i>
                    <div>
                        <h5 class="card-title mb-0"><?= $report['active_users'] ?></h5>
                        <small>Ativos (30min)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-day me-2"></i>
                    <div>
                        <h5 class="card-title mb-0"><?= $report['today_sessions'] ?></h5>
                        <small>Sessões hoje</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-eye me-2"></i>
                    <div>
                        <h5 class="card-title mb-0"><?= count($report['top_pages']) ?></h5>
                        <small>Páginas ativas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($sessions)): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    <strong>Nenhuma sessão ativa encontrada.</strong>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-list"></i> Sessões Ativas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Login</th>
                        <th>Última Atividade</th>
                        <th>Duração</th>
                        <th>Página Atual</th>
                        <th>Páginas</th>
                        <th>IP</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($session['minutes_inactive'] <= 5): ?>
                                    <span class="badge bg-success rounded-pill me-2">●</span>
                                <?php elseif ($session['minutes_inactive'] <= 15): ?>
                                    <span class="badge bg-warning rounded-pill me-2">●</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill me-2">●</span>
                                <?php endif; ?>
                                
                                <div>
                                    <strong><?= htmlspecialchars($session['user_name']) ?></strong>
                                    <br><small class="text-muted">ID: <?= $session['user_id'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small><?= date('d/m H:i', strtotime($session['login_time'])) ?></small>
                        </td>
                        <td>
                            <small>
                                <?= date('d/m H:i:s', strtotime($session['last_activity'])) ?>
                                <br>
                                <span class="text-muted">
                                    <?php if ($session['minutes_inactive'] == 0): ?>
                                        agora mesmo
                                    <?php elseif ($session['minutes_inactive'] == 1): ?>
                                        1 minuto atrás
                                    <?php else: ?>
                                        <?= $session['minutes_inactive'] ?> min atrás
                                    <?php endif; ?>
                                </span>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <?php 
                                $duration = $session['session_duration'];
                                if ($duration >= 60) {
                                    echo floor($duration / 60) . 'h ' . ($duration % 60) . 'm';
                                } else {
                                    echo $duration . 'm';
                                }
                                ?>
                            </span>
                        </td>
                        <td>
                            <code class="small"><?= htmlspecialchars($session['current_page']) ?></code>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= $session['page_count'] ?></span>
                        </td>
                        <td>
                            <small class="font-monospace"><?= htmlspecialchars($session['ip_address']) ?></small>
                        </td>
                        <td>
                            <?php if ($session['minutes_inactive'] <= 5): ?>
                                <span class="badge bg-success">Online</span>
                            <?php elseif ($session['minutes_inactive'] <= 15): ?>
                                <span class="badge bg-warning">Inativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ausente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($session['session_id'] !== session_id()): ?>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="endSession('<?= htmlspecialchars($session['session_id']) ?>')"
                                    title="Encerrar sessão">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            <?php else: ?>
                            <span class="badge bg-primary">Você</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($report['top_pages'])): ?>
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="bi bi-bar-chart"></i> Páginas Mais Visitadas Hoje</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($report['top_pages'] as $page): ?>
            <div class="col-md-6 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <code class="small"><?= htmlspecialchars($page['current_page']) ?></code>
                        <br><small class="text-muted"><?= $page['unique_users'] ?> usuários únicos</small>
                    </div>
                    <span class="badge bg-primary"><?= $page['visits'] ?> visitas</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<script>
function endSession(sessionId) {
    if (confirm('Tem certeza que deseja encerrar esta sessão?')) {
        window.location.href = '/admin/sessions/end/' + sessionId;
    }
}

setTimeout(function() {
    location.reload();
}, 30000);
</script>