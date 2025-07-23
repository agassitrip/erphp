<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\FirstRunService;

class FirstRunController extends BaseController
{
    private FirstRunService $firstRunService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->firstRunService = new FirstRunService();
        
        if (!$this->firstRunService->isFirstRun()) {
            $this->redirect('/');
        }
    }

    public function index(): void
    {
        $this->renderWizard('first-run/welcome', [
            'title' => 'Bem-vindo ao Teste Montink'
        ]);
    }

    public function database(): void
    {
        if ($this->isPost()) {
            $this->processDatabase();
            return;
        }

        $this->renderWizard('first-run/database', [
            'title' => 'Configuração do Banco de Dados'
        ]);
    }

    public function import(): void
    {
        if ($this->isPost()) {
            $this->processImport();
            return;
        }

        $this->renderWizard('first-run/import', [
            'title' => 'Importar Dados'
        ]);
    }

    public function complete(): void
    {
        $this->renderWizard('first-run/complete', [
            'title' => 'Configuração Concluída'
        ]);
    }

    private function processDatabase(): void
    {
        try {
            $config = [
                'host' => trim($_POST['db_host'] ?? 'localhost'),
                'port' => (int)($_POST['db_port'] ?? 3306),
                'database' => trim($_POST['db_database'] ?? ''),
                'username' => trim($_POST['db_username'] ?? ''),
                'password' => $_POST['db_password'] ?? ''
            ];

            if (empty($config['database'])) {
                throw new \Exception('Nome do banco de dados é obrigatório');
            }
            
            if (empty($config['username'])) {
                throw new \Exception('Nome de usuário do banco é obrigatório');
            }

            $connectionResult = $this->firstRunService->testDatabaseConnection($config);
            if (!$connectionResult['success']) {
                throw new \Exception('Erro de conexão: ' . $connectionResult['error']);
            }

            $createDbResult = $this->firstRunService->createDatabase($config);
            if (!$createDbResult['success']) {
                throw new \Exception('Erro ao criar database: ' . $createDbResult['error']);
            }

            $envResult = $this->firstRunService->createEnvFile($config);
            if (!$envResult['success']) {
                throw new \Exception('Erro ao criar configuração: ' . $envResult['error']);
            }

            $_SESSION['wizard_step'] = 'import';
            $_SESSION['flash_success'] = 'Conexão com banco configurada com sucesso!';
            $this->redirect('/first-run/import');

        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/first-run/database');
        }
    }

    private function processImport(): void
    {
        try {
            $importType = $_POST['import_type'] ?? 'default';

            if ($importType === 'default') {
                $result = $this->firstRunService->importDefaultSchema();
                
                if (!$result['success']) {
                    throw new \Exception('Erro ao criar schema: ' . $result['error']);
                }

                $this->firstRunService->markFirstRunComplete();
                $_SESSION['flash_success'] = 'Sistema configurado com sucesso! ' . $result['statements_executed'] . ' tabelas criadas.';
                
            } elseif ($importType === 'backup' && isset($_FILES['backup_file'])) {
                if ($_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('Erro no upload do arquivo');
                }

                $tempPath = $_FILES['backup_file']['tmp_name'];
                
                require_once __DIR__ . '/../Services/BackupService.php';
                $backupService = new \App\Services\BackupService($this->container->get('database'));
                
                $result = $backupService->restoreFromSqlFile($tempPath);
                
                if (!$result['success']) {
                    throw new \Exception('Erro ao restaurar backup: ' . $result['error']);
                }

                $this->firstRunService->markFirstRunComplete();
                $_SESSION['flash_success'] = 'Backup restaurado com sucesso! ' . $result['statements_executed'] . ' comandos executados.';
            }

            $_SESSION['wizard_step'] = 'complete';
            $this->redirect('/first-run/complete');

        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('/first-run/import');
        }
    }

    protected function renderWizard(string $view, array $data = []): void
    {
        $content = $this->renderView($view, $data);
        echo $this->renderView('first-run/layout', array_merge($data, ['content' => $content]));
    }
    
    private function renderView(string $view, array $data = []): string
    {
        extract($data);
        
        ob_start();
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>View não encontrada: $view</h1>";
        }
        
        return ob_get_clean();
    }
}