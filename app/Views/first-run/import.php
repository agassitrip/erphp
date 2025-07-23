<div class="text-center mb-4">
    <i class="bi bi-cloud-download text-success" style="font-size: 3rem;"></i>
    <h3 class="mt-3">Importar Dados</h3>
    <p class="text-muted">Escolha como inicializar seu sistema</p>
</div>

<form method="POST" action="/first-run/import" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-10 mx-auto">
            
            <!-- Opção Padrão -->
            <div class="card mb-3 border-primary">
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="import_type" id="default_schema" 
                               value="default" checked onchange="toggleBackupUpload()">
                        <label class="form-check-label" for="default_schema">
                            <h5 class="mb-2">
                                <i class="bi bi-stars text-primary"></i>
                                Configuração Padrão (Recomendado)
                            </h5>
                            <p class="text-muted mb-2">
                                Cria automaticamente todas as tabelas necessárias e adiciona dados de exemplo.
                            </p>
                            <small class="text-primary">
                                <i class="bi bi-check-circle"></i> Usuários: admin@teste.com / user@teste.com (senha: password)<br>
                                <i class="bi bi-check-circle"></i> Produtos de demonstração<br>
                                <i class="bi bi-check-circle"></i> Configurações otimizadas
                            </small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Opção Backup -->
            <div class="card mb-4 border-secondary">
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="import_type" id="backup_restore" 
                               value="backup" onchange="toggleBackupUpload()">
                        <label class="form-check-label" for="backup_restore">
                            <h5 class="mb-2">
                                <i class="bi bi-upload text-warning"></i>
                                Restaurar de Backup
                            </h5>
                            <p class="text-muted mb-2">
                                Importa dados de um arquivo de backup existente (.sql).
                            </p>
                        </label>
                    </div>
                    
                    <div id="backup_upload_section" style="display: none;" class="mt-3">
                        <div class="mb-3">
                            <label for="backup_file" class="form-label">
                                <i class="bi bi-file-earmark-zip"></i> Arquivo de Backup
                            </label>
                            <input type="file" class="form-control" id="backup_file" name="backup_file" 
                                   accept=".sql">
                        </div>
                        
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-6">
                    <a href="/first-run/database" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-rocket"></i> Finalizar Configuração
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function toggleBackupUpload() {
    const backupRadio = document.getElementById('backup_restore');
    const uploadSection = document.getElementById('backup_upload_section');
    const fileInput = document.getElementById('backup_file');
    
    if (backupRadio.checked) {
        uploadSection.style.display = 'block';
        fileInput.required = true;
    } else {
        uploadSection.style.display = 'none';
        fileInput.required = false;
    }
}
</script>

<?php $_SESSION['wizard_step'] = 'import'; ?>