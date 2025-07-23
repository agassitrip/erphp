<?php
use App\Helpers\ViewHelper;
ob_start();
$isEdit = isset($supplier);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-truck"></i> 
        <?= $isEdit ? 'Editar Fornecedor' : 'Novo Fornecedor' ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/fornecedores" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST">
                    <?= ViewHelper::csrfField() ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Razão Social *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($supplier['name'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($supplier['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone *</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document" class="form-label">CNPJ *</label>
                                <input type="text" class="form-control" id="document" name="document" 
                                       value="<?= htmlspecialchars($supplier['document'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Pessoa de Contato *</label>
                                <input type="text" class="form-control" id="contact" name="contact" 
                                       value="<?= htmlspecialchars($supplier['contact'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Endereço *</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= htmlspecialchars($supplier['address'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city" class="form-label">Cidade *</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= htmlspecialchars($supplier['city'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="state" class="form-label">Estado *</label>
                                <select class="form-select" id="state" name="state" required>
                                    <option value="">Selecione</option>
                                    <option value="SP" <?= ($supplier['state'] ?? '') == 'SP' ? 'selected' : '' ?>>SP</option>
                                    <option value="RJ" <?= ($supplier['state'] ?? '') == 'RJ' ? 'selected' : '' ?>>RJ</option>
                                    <option value="MG" <?= ($supplier['state'] ?? '') == 'MG' ? 'selected' : '' ?>>MG</option>
                                    <option value="RS" <?= ($supplier['state'] ?? '') == 'RS' ? 'selected' : '' ?>>RS</option>
                                    <option value="PR" <?= ($supplier['state'] ?? '') == 'PR' ? 'selected' : '' ?>>PR</option>
                                    <option value="SC" <?= ($supplier['state'] ?? '') == 'SC' ? 'selected' : '' ?>>SC</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="zip_code" class="form-label">CEP *</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                       value="<?= htmlspecialchars($supplier['zip_code'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/fornecedores" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> <?= $isEdit ? 'Atualizar' : 'Salvar' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = ($isEdit ? 'Editar' : 'Novo') . ' Fornecedor - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
