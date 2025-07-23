<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Teste Montink - Configuração Inicial' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .wizard-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .wizard-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }
        .wizard-header {
            background: linear-gradient(135deg, var(--primary-color), #4dabf7);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .wizard-body {
            padding: 2rem;
        }
        .wizard-progress {
            height: 6px;
            background: #e9ecef;
        }
        .wizard-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--success-color), #20c997);
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="wizard-container">
        <div class="wizard-card">
            <div class="wizard-header">
                <i class="bi bi-gear-fill" style="font-size: 3rem;"></i>
                <h1 class="h3 mt-3 mb-0"><?= $title ?? 'Configuração Inicial' ?></h1>
            </div>
            
            <!-- Progress Bar -->
            <div class="wizard-progress">
                <div class="wizard-progress-bar" style="width: <?= getProgressWidth() ?>%"></div>
            </div>
            
            <div class="wizard-body">
                <!-- Flash Messages -->
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php
        function getProgressWidth() {
            $step = $_SESSION['wizard_step'] ?? 'welcome';
            switch ($step) {
                case 'welcome': return 25;
                case 'database': return 50;
                case 'import': return 75;
                case 'complete': return 100;
                default: return 25;
            }
        }
        ?>
    </script>
</body>
</html>