<?php

declare(strict_types=1);

namespace App\Helpers;

class RedirectHelper
{
    public static function getHomeUrl(): string
    {
        return ($_SESSION['user_role'] ?? '') === 'admin' ? '/admin' : '/';
    }
    
    public static function getBrandUrl(): string
    {
        return self::getHomeUrl();
    }
    
    public static function getBrandText(): string
    {
        return ($_SESSION['user_role'] ?? '') === 'admin' ? 'Painel Admin' : 'Teste Montink';
    }
    
    public static function getBrandIcon(): string
    {
        return ($_SESSION['user_role'] ?? '') === 'admin' ? 'bi-speedometer2' : 'bi-shop';
    }
}