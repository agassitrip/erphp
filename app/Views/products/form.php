<?php
use App\Helpers\ViewHelper;
ob_start();
$isEdit = isset($product);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-box"></i> 
        <?= $isEdit ? 'Editar Produto' : 'Novo Produto' ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/produtos" class="btn btn-secondary">
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
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code" class="form-label">Código *</label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="<?= htmlspecialchars($product['code'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Preço de Venda *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" min="0" value="<?= $product['price'] ?? '' ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost" class="form-label">Custo *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="cost" name="cost" 
                                           step="0.01" min="0" value="<?= $product['cost'] ?? '' ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Estoque Atual</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       min="0" value="<?= $product['stock'] ?? '0' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_stock" class="form-label">Estoque Mínimo</label>
                                <input type="number" class="form-control" id="min_stock" name="min_stock" 
                                       min="0" value="<?= $product['min_stock'] ?? '0' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    <option value="1" <?= ($product['category_id'] ?? '') == '1' ? 'selected' : '' ?>>Eletrônicos</option>
                                    <option value="2" <?= ($product['category_id'] ?? '') == '2' ? 'selected' : '' ?>>Roupas</option>
                                    <option value="3" <?= ($product['category_id'] ?? '') == '3' ? 'selected' : '' ?>>Casa e Jardim</option>
                                    <option value="4" <?= ($product['category_id'] ?? '') == '4' ? 'selected' : '' ?>>Esportes</option>
                                    <option value="5" <?= ($product['category_id'] ?? '') == '5' ? 'selected' : '' ?>>Livros</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Fornecedor *</label>
                                <select class="form-select" id="supplier_id" name="supplier_id" required>
                                    <option value="">Selecione um fornecedor</option>
                                    <option value="1" <?= ($product['supplier_id'] ?? '') == '1' ? 'selected' : '' ?>>Tech Distribuidora LTDA</option>
                                    <option value="2" <?= ($product['supplier_id'] ?? '') == '2' ? 'selected' : '' ?>>Moda & Estilo LTDA</option>
                                    <option value="3" <?= ($product['supplier_id'] ?? '') == '3' ? 'selected' : '' ?>>Casa Bella LTDA</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/produtos" class="btn btn-secondary me-md-2">Cancelar</a>
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
$title = ($isEdit ? 'Editar' : 'Novo') . ' Produto - Teste Montink';
require_once __DIR__ . '/../layout.php';
?>
