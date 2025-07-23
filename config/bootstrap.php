<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Container;

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$container = Container::getInstance();

$container->bind('database', function() {
    return new Database([
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'dbname' => $_ENV['DB_DATABASE'] ?? 'erp_system',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4'
    ]);
});

$container->bind('auth', function() use ($container) {
    return new App\Services\AuthService(
        new App\Repositories\UserRepository($container->get('database'))
    );
});
