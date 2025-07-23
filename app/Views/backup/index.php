<?php
use App\Helpers\ViewHelper;
use App\Helpers\MobileComponent;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-database"></i> Gerenciar Backups</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <form method="POST" action="/admin/backup/create" class="d-inline">
            <button type="submit" class="btn btn-success me-2">
                <i class="bi bi-download"></i> Criar Backup
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Desktop Table -->
        <div class="card shadow d-none d-md-block">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-archive"></i> Backups Disponíveis</h5>
            </div>
            <div class="card-body">
                <?php if (empty($backups)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Nenhum backup encontrado. Crie seu primeiro backup!
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Arquivo</th>
                                <th>Data de Criação</th>
                                <th>Tamanho</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-file-earmark-zip text-primary"></i>
                                    <?= htmlspecialchars($backup['filename']) ?>
                                </td>
                                <td><?= $backup['created_at'] ?></td>
                                <td><?= $backup['human_size'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/backup/download/<?= urlencode($backup['filename']) ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="restoreBackup('<?= htmlspecialchars($backup['filename']) ?>')">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteBackup('<?= htmlspecialchars($backup['filename']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Cards -->
        <?php if (!empty($backups)): ?>
        <div class="d-block d-md-none">
            <?php foreach ($backups as $backup): ?>
            <div class="mobile-card">
                <div class="mobile-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="mobile-table-main">
                            <div class="mobile-table-title">
                                <i class="bi bi-file-earmark-zip text-primary"></i>
                                <?= htmlspecialchars($backup['filename']) ?>
                            </div>
                            <div class="mobile-table-subtitle">
                                <?= $backup['created_at'] ?> • <?= $backup['human_size'] ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <a href="/admin/backup/download/<?= urlencode($backup['filename']) ?>" 
                           class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-download"></i> Download
                        </a>
                        <button type="button" class="btn btn-success btn-sm flex-fill" 
                                onclick="restoreBackup('<?= htmlspecialchars($backup['filename']) ?>')">
                            <i class="bi bi-arrow-clockwise"></i> Restaurar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="deleteBackup('<?= htmlspecialchars($backup['filename']) ?>')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="d-block d-md-none">
            <?= MobileComponent::mobileAlert(
                'Nenhum backup encontrado. Crie seu primeiro backup!', 
                'info'
            ) ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <!-- Upload Backup -->
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Enviar Backup</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/backup/upload" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="backup_upload" class="form-label">Arquivo SQL</label>
                        <input type="file" class="form-control" id="backup_upload" name="backup_upload" 
                               accept=".sql" required>
                        <small class="text-muted">Máximo 50MB</small>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Enviar Backup
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Backup Info -->
        <div class="card shadow mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Backup Completo:</strong><br>
                    • Todas as tabelas<br>
                    • Estrutura e dados<br>
                    • Configurações<br><br>
                    
                    <strong>Restauração:</strong><br>
                    • Substitui dados atuais<br>
                    • Faça backup antes<br>
                    • Processo irreversível<br><br>
                    
                    <strong>Suporte:</strong><br>
                    • Arquivos .sql<br>
                    • MySQL/MariaDB<br>
                    • UTF-8 encoding
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms -->
<form id="restoreForm" method="POST" action="/admin/backup/restore" style="display: none;">
    <input type="hidden" name="backup_file" id="restoreFile">
</form>

<form id="deleteForm" method="POST" action="" style="display: none;">
</form>

<?= MobileComponent::mobileFab('/admin/backup/create', 'bi-download', 'Criar Backup') ?>

<script>
function restoreBackup(filename) {
    confirmDelete(
        'Tem certeza que deseja restaurar este backup? Todos os dados atuais serão substituídos!',
        function() {
            document.getElementById('restoreFile').value = filename;
            document.getElementById('restoreForm').submit();
        }
    );
}

function deleteBackup(filename) {
    confirmDelete(
        'Tem certeza que deseja deletar este backup?',
        function() {
            const form = document.getElementById('deleteForm');
            form.action = '/admin/backup/delete/' + encodeURIComponent(filename);
            form.submit();
        }
    );
}
</script>