<?php
$title = 'Cadastre-se - Teste Montink';
use App\Helpers\MobileComponent;
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <!-- Desktop Form -->
        <div class="card shadow d-none d-md-block">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus display-4 text-success"></i>
                    <h2 class="mt-3">Cadastre-se</h2>
                    <p class="text-muted">Crie sua conta para acessar a loja completa</p>
                </div>

                <form method="POST" action="/register">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha *</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               minlength="6" required>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Senha *</label>
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" minlength="6" required>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-person-check"></i> Criar Conta
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-muted">
                        Já tem uma conta? 
                        <a href="/login" class="text-decoration-none">Faça login</a>
                    </p>
                    <p class="text-muted">
                        <a href="/shop" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Voltar à loja
                        </a>
                    </p>
                </div>

                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <i class="bi bi-shield-check"></i>
                        <strong>Benefícios da conta:</strong><br>
                        • Histórico de pedidos<br>
                        • Cupons exclusivos<br>
                        • Checkout mais rápido<br>
                        • Ofertas personalizadas
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Mobile Form -->
        <div class="d-block d-md-none px-3">
            <div class="mobile-card">
                <div class="mobile-card-header text-center">
                    <i class="bi bi-person-plus me-2"></i>Cadastre-se
                </div>
                <div class="mobile-card-body">
                    <p class="text-center text-muted mb-4">Crie sua conta para acessar a loja completa</p>
                    
                    <form method="POST" action="/register">
                        <?php 
                        $formFields = [
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'label' => 'Nome Completo',
                                'value' => $_SESSION['old_input']['name'] ?? '',
                                'required' => true
                            ],
                            [
                                'name' => 'email',
                                'type' => 'email',
                                'label' => 'E-mail',
                                'value' => $_SESSION['old_input']['email'] ?? '',
                                'required' => true
                            ],
                            [
                                'name' => 'password',
                                'type' => 'password',
                                'label' => 'Senha (mín. 6 caracteres)',
                                'value' => '',
                                'required' => true
                            ],
                            [
                                'name' => 'confirm_password',
                                'type' => 'password',
                                'label' => 'Confirmar Senha',
                                'value' => '',
                                'required' => true
                            ]
                        ];
                        
                        echo MobileComponent::mobileFloatingForm($formFields);
                        ?>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-person-check"></i> Criar Conta
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-2">
                            Já tem uma conta? 
                            <a href="/login" class="text-decoration-none fw-bold">Faça login</a>
                        </p>
                        <p class="text-muted">
                            <a href="/shop" class="text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Voltar à loja
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Benefits Card -->
            <div class="mobile-card mt-3">
                <div class="mobile-card-body text-center">
                    <i class="bi bi-shield-check text-success mb-2" style="font-size: 2rem;"></i>
                    <h6 class="fw-bold mb-3">Benefícios da conta</h6>
                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">
                                • Histórico de pedidos<br>
                                • Cupons exclusivos
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">
                                • Checkout rápido<br>
                                • Ofertas personalizadas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['old_input']); ?>