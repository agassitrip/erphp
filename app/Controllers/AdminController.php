<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\ErrorLogService;
use App\Services\SessionMonitorService;

class AdminController extends BaseController
{
    private ErrorLogService $errorLogService;
    private SessionMonitorService $sessionMonitorService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->errorLogService = new ErrorLogService();
        $this->sessionMonitorService = new SessionMonitorService($this->container->get('database'));
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->flashError('Acesso negado. Você precisa ser administrador para acessar esta área.');
            $this->redirect('/');
        }
    }

    public function show404Logs(): void
    {
        $logs = $this->errorLogService->getAllLogs();
        
        $this->render('admin/404-logs', [
            'title' => 'Logs de Erro 404 - Teste Montink',
            'logs' => $logs
        ]);
    }
    
    public function show404LogDetail(string $ticketId): void
    {
        $log = $this->errorLogService->getLogById($ticketId);
        
        if (!$log) {
            http_response_code(404);
            echo "Log não encontrado";
            return;
        }
        
        $this->render('admin/404-log-detail', [
            'title' => 'Detalhes do Log 404 - Teste Montink',
            'log' => $log
        ]);
    }
    
    public function showActiveSessions(): void
    {
        $this->sessionMonitorService->createSessionTable();
        
        $sessions = $this->sessionMonitorService->getActiveSessions();
        $report = $this->sessionMonitorService->getSessionReport();
        
        $this->render('admin/active-sessions', [
            'title' => 'Sessões Ativas - Teste Montink',
            'sessions' => $sessions,
            'report' => $report
        ]);
    }
    
    public function endSession(string $sessionId): void
    {
        if ($this->sessionMonitorService->endUserSession($sessionId)) {
            $_SESSION['flash_success'] = 'Sessão encerrada com sucesso.';
        } else {
            $_SESSION['flash_error'] = 'Erro ao encerrar sessão.';
        }
        
        header('Location: /admin/sessions');
        exit;
    }
    
    public function getUserStats(string $userId): void
    {
        $stats = $this->sessionMonitorService->getUserSessionStats((int)$userId);
        
        header('Content-Type: application/json');
        echo json_encode($stats);
    }
}