<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\BackupService;

class BackupController extends BaseController
{
    private BackupService $backupService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->requireAdmin();
        $this->backupService = new BackupService($this->container->get('database'));
    }

    public function index(): void
    {
        $backups = $this->backupService->getAvailableBackups();
        
        $this->render('backup/index', [
            'title' => 'Gerenciar Backups - Teste Montink',
            'backups' => $backups
        ]);
    }

    public function create(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/backup');
            return;
        }

        try {
            $result = $this->backupService->createFullBackup();
            
            if ($result['success']) {
                $exportResult = $this->backupService->exportToSqlFile($result['backup']);
                
                if ($exportResult['success']) {
                    $this->flashSuccess('Backup criado com sucesso! Arquivo: ' . $exportResult['filename']);
                } else {
                    $this->flashError('Backup criado mas erro ao salvar arquivo: ' . $exportResult['error']);
                }
            } else {
                $this->flashError('Erro ao criar backup: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            $this->flashError('Erro inesperado: ' . $e->getMessage());
        }

        $this->redirect('/admin/backup');
    }

    public function download(string $filename): void
    {
        $backups = $this->backupService->getAvailableBackups();
        $backup = null;
        
        foreach ($backups as $b) {
            if ($b['filename'] === $filename) {
                $backup = $b;
                break;
            }
        }
        
        if (!$backup || !file_exists($backup['filepath'])) {
            $this->flashError('Arquivo de backup não encontrado');
            $this->redirect('/admin/backup');
            return;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($backup['filepath']));
        header('Cache-Control: no-cache, must-revalidate');
        
        readfile($backup['filepath']);
        exit;
    }

    public function restore(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/backup');
            return;
        }

        $filename = $_POST['backup_file'] ?? '';
        
        if (empty($filename)) {
            $this->flashError('Nenhum arquivo de backup selecionado');
            $this->redirect('/admin/backup');
            return;
        }

        $backups = $this->backupService->getAvailableBackups();
        $backup = null;
        
        foreach ($backups as $b) {
            if ($b['filename'] === $filename) {
                $backup = $b;
                break;
            }
        }
        
        if (!$backup) {
            $this->flashError('Arquivo de backup não encontrado');
            $this->redirect('/admin/backup');
            return;
        }

        try {
            $result = $this->backupService->restoreFromSqlFile($backup['filepath']);
            
            if ($result['success']) {
                $this->flashSuccess('Backup restaurado com sucesso! ' . $result['statements_executed'] . ' comandos executados.');
            } else {
                $this->flashError('Erro ao restaurar backup: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            $this->flashError('Erro inesperado: ' . $e->getMessage());
        }

        $this->redirect('/admin/backup');
    }

    public function upload(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/backup');
            return;
        }

        if (!isset($_FILES['backup_upload']) || $_FILES['backup_upload']['error'] !== UPLOAD_ERR_OK) {
            $this->flashError('Erro no upload do arquivo');
            $this->redirect('/admin/backup');
            return;
        }

        $uploadedFile = $_FILES['backup_upload'];
        
        if ($uploadedFile['size'] > 50 * 1024 * 1024) {
            $this->flashError('Arquivo muito grande (máximo 50MB)');
            $this->redirect('/admin/backup');
            return;
        }

        $filename = 'backup_uploaded_' . date('Y-m-d_H-i-s') . '.sql';
        $destinationPath = __DIR__ . '/../../storage/backups/' . $filename;
        
        if (!move_uploaded_file($uploadedFile['tmp_name'], $destinationPath)) {
            $this->flashError('Erro ao salvar arquivo de backup');
            $this->redirect('/admin/backup');
            return;
        }

        $this->flashSuccess('Arquivo de backup enviado com sucesso: ' . $filename);
        $this->redirect('/admin/backup');
    }

    public function delete(string $filename): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/backup');
            return;
        }

        $backups = $this->backupService->getAvailableBackups();
        $backup = null;
        
        foreach ($backups as $b) {
            if ($b['filename'] === $filename) {
                $backup = $b;
                break;
            }
        }
        
        if (!$backup || !file_exists($backup['filepath'])) {
            $this->flashError('Arquivo de backup não encontrado');
            $this->redirect('/admin/backup');
            return;
        }

        if (unlink($backup['filepath'])) {
            $this->flashSuccess('Backup deletado com sucesso');
        } else {
            $this->flashError('Erro ao deletar arquivo de backup');
        }

        $this->redirect('/admin/backup');
    }
}