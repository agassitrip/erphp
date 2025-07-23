<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Teste Montink' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/shop">
                <i class="bi bi-code-square"></i> Teste Montink
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/shop"><i class="bi bi-shop"></i> Loja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/shop/cart"><i class="bi bi-cart"></i> Carrinho</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a class="dropdown-item" href="/admin">
                                <i class="bi bi-speedometer2"></i> Painel Admin
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/logout">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">
                            <i class="bi bi-person-plus"></i> Cadastre-se
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4">
        <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i>
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i>
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-code-square"></i> Teste Montink</h5>
                    <p class="text-muted">Teste Montink</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted">
                        <i class="bi bi-shield-check"></i> Compra segura
                        <br>
                        <i class="bi bi-truck"></i> Entrega rápida
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <?php
    use App\Helpers\MobileComponent;
    
    $mobileNavItems = [
        ['url' => '/shop', 'icon' => 'bi-shop', 'label' => 'Loja', 'page' => 'shop'],
        ['url' => '/shop/cart', 'icon' => 'bi-cart', 'label' => 'Carrinho', 'page' => 'cart']
    ];
    
    if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
        $mobileNavItems[] = ['url' => '/', 'icon' => 'bi-speedometer2', 'label' => 'Admin', 'page' => 'admin'];
    }
    
    if (isset($_SESSION['user_id'])) {
        $mobileNavItems[] = ['url' => '/logout', 'icon' => 'bi-box-arrow-right', 'label' => 'Sair', 'page' => 'logout'];
    } else {
        $mobileNavItems[] = ['url' => '/register', 'icon' => 'bi-person-plus', 'label' => 'Cadastrar', 'page' => 'register'];
        $mobileNavItems[] = ['url' => '/login', 'icon' => 'bi-box-arrow-in-right', 'label' => 'Login', 'page' => 'login'];
    }
    
    $currentPage = '';
    if (strpos($_SERVER['REQUEST_URI'], '/shop/cart') !== false) $currentPage = 'cart';
    elseif (strpos($_SERVER['REQUEST_URI'], '/shop') !== false) $currentPage = 'shop';
    elseif (strpos($_SERVER['REQUEST_URI'], '/register') !== false) $currentPage = 'register';
    elseif (strpos($_SERVER['REQUEST_URI'], '/login') !== false) $currentPage = 'login';
    elseif ($_SERVER['REQUEST_URI'] === '/') $currentPage = 'admin';
    
    echo MobileComponent::mobileNavigation($mobileNavItems, $currentPage);
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>