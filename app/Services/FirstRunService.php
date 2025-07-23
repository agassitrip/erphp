<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use PDO;

class FirstRunService
{
    private string $configPath;
    private string $flagPath;

    public function __construct()
    {
        $this->configPath = __DIR__ . '/../../.env';
        $this->flagPath = __DIR__ . '/../../storage/.first_run_complete';
    }

    public function isFirstRun(): bool
    {
        return !file_exists($this->flagPath) || !file_exists($this->configPath);
    }

    public function markFirstRunComplete(): void
    {
        if (!is_dir(dirname($this->flagPath))) {
            mkdir(dirname($this->flagPath), 0755, true);
        }
        
        file_put_contents($this->flagPath, date('Y-m-d H:i:s'));
    }

    public function testDatabaseConnection(array $config): array
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            return [
                'success' => true,
                'message' => 'Conexão estabelecida com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function createDatabase(array $config): array
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            $stmt = $pdo->prepare("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $stmt->execute();

            return [
                'success' => true,
                'message' => 'Database criado com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function createEnvFile(array $config): array
    {
        try {
            $envContent = $this->generateEnvContent($config);
            
            if (file_put_contents($this->configPath, $envContent) === false) {
                throw new Exception('Erro ao criar arquivo .env');
            }

            return [
                'success' => true,
                'message' => 'Arquivo de configuração criado'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function importDefaultSchema(): array
    {
        try {
            $schemaPath = __DIR__ . '/../../database/schema.sql';
            
            if (!file_exists($schemaPath)) {
                return $this->createDefaultSchema();
            }

            $sql = file_get_contents($schemaPath);
            if ($sql === false) {
                throw new Exception('Erro ao ler schema padrão');
            }

            $pdo = $this->getDatabaseConnection();

            $statements = $this->splitSqlStatements($sql);
            $executedCount = 0;
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement) || strpos($statement, '--') === 0) {
                    continue;
                }
                
                $pdo->exec($statement);
                $executedCount++;
            }

            return [
                'success' => true,
                'statements_executed' => $executedCount,
                'message' => 'Schema importado com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function createDefaultSchema(): array
    {
        try {
            $pdo = $this->getDatabaseConnection();

            $tables = $this->getDefaultTables();
            $executedCount = 0;

            foreach ($tables as $sql) {
                $pdo->exec($sql);
                $executedCount++;
            }

            $this->insertDefaultData($pdo);

            return [
                'success' => true,
                'statements_executed' => $executedCount,
                'message' => 'Schema padrão criado com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function generateEnvContent(array $config): string
    {
        return "# Configuração do Banco de Dados
DB_HOST={$config['host']}
DB_PORT={$config['port']}
DB_DATABASE={$config['database']}
DB_USERNAME={$config['username']}
DB_PASSWORD={$config['password']}

# Configurações da Aplicação
APP_NAME=Teste Montink
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# Configuração de Sessão
SESSION_LIFETIME=120
";
    }

    private function getDefaultTables(): array
    {
        return [
            // Users table
            "CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `role` enum('admin','user') DEFAULT 'user',
                `active` tinyint(1) DEFAULT 1,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Products table
            "CREATE TABLE IF NOT EXISTS `products` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `code` varchar(100) NOT NULL UNIQUE,
                `description` text,
                `price` decimal(10,2) NOT NULL,
                `stock` int(11) DEFAULT 0,
                `min_stock` int(11) DEFAULT 5,
                `category_id` int(11) DEFAULT NULL,
                `supplier_id` int(11) DEFAULT NULL,
                `active` tinyint(1) DEFAULT 1,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Customers table
            "CREATE TABLE IF NOT EXISTS `customers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) DEFAULT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `document` varchar(20) DEFAULT NULL,
                `address` text,
                `active` tinyint(1) DEFAULT 1,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Suppliers table
            "CREATE TABLE IF NOT EXISTS `suppliers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `contact_name` varchar(255) DEFAULT NULL,
                `email` varchar(255) DEFAULT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `address` text,
                `active` tinyint(1) DEFAULT 1,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Sales table
            "CREATE TABLE IF NOT EXISTS `sales` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `customer_id` int(11) DEFAULT NULL,
                `customer_name` varchar(255) DEFAULT NULL,
                `customer_email` varchar(255) DEFAULT NULL,
                `customer_phone` varchar(20) DEFAULT NULL,
                `subtotal` decimal(10,2) NOT NULL,
                `discount` decimal(10,2) DEFAULT 0.00,
                `total` decimal(10,2) NOT NULL,
                `payment_method` varchar(50) DEFAULT NULL,
                `status` enum('pending','completed','cancelled') DEFAULT 'pending',
                `user_id` int(11) NOT NULL,
                `shipping_cost` decimal(10,2) DEFAULT 0.00,
                `coupon_code` varchar(50) DEFAULT NULL,
                `coupon_discount` decimal(10,2) DEFAULT 0.00,
                `customer_cep` varchar(10) DEFAULT NULL,
                `customer_address` text,
                `customer_city` varchar(100) DEFAULT NULL,
                `customer_state` varchar(50) DEFAULT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Sale items table
            "CREATE TABLE IF NOT EXISTS `sale_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `sale_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `quantity` int(11) NOT NULL,
                `price` decimal(10,2) NOT NULL,
                `total` decimal(10,2) NOT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `sale_id` (`sale_id`),
                KEY `product_id` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
    }

    private function insertDefaultData(\PDO $pdo): void
    {
        $adminPasswordHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`, `active`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@teste.com', $adminPasswordHash, 'admin', 1]);
        
        $userPasswordHash = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`, `active`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Usuário Teste', 'user@teste.com', $userPasswordHash, 'user', 1]);
        

        $pdo->exec("INSERT IGNORE INTO `products` (`id`, `name`, `code`, `description`, `price`, `stock`) VALUES 
            (1, 'Produto Exemplo 1', 'PROD001', 'Produto de demonstração', 99.90, 10),
            (2, 'Produto Exemplo 2', 'PROD002', 'Outro produto de teste', 149.99, 15),
            (3, 'Produto Exemplo 3', 'PROD003', 'Mais um produto', 79.50, 8)");
    }

    private function getDatabaseConnection(): PDO
    {
        if (file_exists($this->configPath)) {
            $lines = file($this->configPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $config = [];
            
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
                    continue;
                }
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $config[trim($parts[0])] = trim($parts[1]);
                }
            }
            
            $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_DATABASE']};charset=utf8mb4";
            return new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
        
        throw new Exception('Arquivo de configuração não encontrado. Execute a configuração do banco primeiro.');
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar) {
                $inString = false;
                $stringChar = '';
            }
            
            if (!$inString && $char === ';') {
                $statements[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        if (trim($current) !== '') {
            $statements[] = trim($current);
        }
        
        return $statements;
    }
}