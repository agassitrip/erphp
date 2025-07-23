<?php

declare(strict_types=1);

namespace App\Services;

class ErrorLogService
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = __DIR__ . '/../../storage/logs/404/';
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    public function log404(string $uri, array $context = []): string
    {
        $ticketId = uniqid('404_', true);
        $timestamp = date('Y-m-d H:i:s');
        
        $logData = [
            'ticket_id' => $ticketId,
            'timestamp' => $timestamp,
            'uri' => $uri,
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? null,
            'available_routes' => $this->getAvailableRoutes(),
            'context' => $context
        ];
        
        $filename = $this->logPath . $ticketId . '.json';
        file_put_contents($filename, json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $ticketId;
    }
    
    public function getLogById(string $ticketId): ?array
    {
        $filename = $this->logPath . $ticketId . '.json';
        if (!file_exists($filename)) {
            return null;
        }
        
        $content = file_get_contents($filename);
        return json_decode($content, true);
    }
    
    public function getAllLogs(): array
    {
        $logs = [];
        $files = glob($this->logPath . '*.json');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $logData = json_decode($content, true);
            if ($logData) {
                $logs[] = $logData;
            }
        }
        
        usort($logs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return $logs;
    }
    
    private function getAvailableRoutes(): array
    {
        return [
            'Dashboard' => '/',
            'Produtos' => [
                'Listar Produtos' => '/produtos',
                'Novo Produto' => '/produtos/create'
            ],
            'Clientes' => [
                'Listar Clientes' => '/clientes',
                'Novo Cliente' => '/clientes/create'
            ],
            'Fornecedores' => [
                'Listar Fornecedores' => '/fornecedores',
                'Novo Fornecedor' => '/fornecedores/create'
            ],
            'Vendas' => [
                'Listar Vendas' => '/vendas',
                'Nova Venda' => '/vendas/create'
            ],
            'Relatórios' => [
                'Vendas' => '/relatorios/vendas',
                'Estoque' => '/relatorios/estoque',
                'Financeiro' => '/relatorios/financeiro'
            ],
            'Usuários' => '/usuarios'
        ];
    }
}