<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\SessionMonitorService;

abstract class BaseController
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->trackUserActivity();
    }
    
    private function trackUserActivity(): void
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
            try {
                $sessionMonitor = new SessionMonitorService($this->container->get('database'));
                $sessionMonitor->createSessionTable();
                $sessionMonitor->trackUserActivity((int)$_SESSION['user_id'], $_SESSION['user_name']);
            } catch (\Exception $e) {
                error_log("Session tracking error: " . $e->getMessage());
            }
        }
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function flashSuccess(string $message): void
    {
        $_SESSION['flash_success'] = $message;
    }

    protected function flashError(string $message): void
    {
        $_SESSION['flash_error'] = $message;
    }

    protected function view(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../Views/{$template}.php";
    }
    
    protected function render(string $view, array $data = []): void
    {
        $content = $this->renderView($view, $data);
        echo $this->renderView('layout', array_merge($data, ['content' => $content]));
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

    protected function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            $this->flashError('Acesso negado');
            $this->redirect('/');
        }
    }
}
