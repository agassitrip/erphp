<?php

declare(strict_types=1);

namespace App\Helpers;

class ViewHelper
{
    public static function formatCurrency(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public static function formatDate(string $date): string
    {
        return date('d/m/Y', strtotime($date));
    }

    public static function formatDateTime(string $datetime): string
    {
        return date('d/m/Y H:i', strtotime($datetime));
    }

    public static function statusBadge(int $active): string
    {
        if ($active) {
            return '<span class="badge bg-success">Ativo</span>';
        }
        return '<span class="badge bg-danger">Inativo</span>';
    }

    public static function stockAlert(int $stock, int $minStock): string
    {
        if ($stock <= 0) {
            return '<span class="badge bg-danger">Sem Estoque</span>';
        }
        
        if ($stock <= $minStock) {
            return '<span class="badge bg-warning">Estoque Baixo</span>';
        }
        
        return '<span class="badge bg-success">Normal</span>';
    }

    public static function paymentMethodBadge(string $method): string
    {
        $badges = [
            'cash' => '<span class="badge bg-success">Dinheiro</span>',
            'card' => '<span class="badge bg-primary">Cartão</span>',
            'pix' => '<span class="badge bg-info">PIX</span>',
            'transfer' => '<span class="badge bg-secondary">Transferência</span>'
        ];
        
        return $badges[$method] ?? '<span class="badge bg-light">Desconhecido</span>';
    }

    public static function roleLabel(string $role): string
    {
        $roles = [
            'admin' => 'Administrador',
            'user' => 'Usuário'
        ];
        
        return $roles[$role] ?? 'Desconhecido';
    }

    public static function truncate(string $text, int $length = 50): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }

    public static function flashMessages(): string
    {
        $html = '';
        
        if (isset($_SESSION['flash_success'])) {
            $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            $html .= htmlspecialchars($_SESSION['flash_success']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
            unset($_SESSION['flash_success']);
        }
        
        if (isset($_SESSION['flash_error'])) {
            $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            $html .= htmlspecialchars($_SESSION['flash_error']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
            unset($_SESSION['flash_error']);
        }
        
        return $html;
    }

    public static function csrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::csrfToken() . '">';
    }

    public static function formatDocument(string $document): string
    {
        $document = preg_replace('/\D/', '', $document);
        
        if (strlen($document) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document);
        }
        
        if (strlen($document) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document);
        }
        
        return $document;
    }

    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        if (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return $phone;
    }

    public static function formatZipCode(string $zipCode): string
    {
        $zipCode = preg_replace('/\D/', '', $zipCode);
        
        if (strlen($zipCode) === 8) {
            return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipCode);
        }
        
        return $zipCode;
    }
}
