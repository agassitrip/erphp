<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;
use Exception;

class MigrationService
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function runMigrations(): array
    {
        $results = [];
        $transactionStarted = false;
        
        try {
            $results[] = $this->createCouponsTable();

            $results[] = $this->createProductVariationsTable();

            $results[] = $this->createStockTable();

            $results[] = $this->updateSalesTable();

            $this->db->beginTransaction();
            $transactionStarted = true;

            $results[] = $this->migrateExistingStock();

            $results[] = $this->insertSampleCoupons();

            $this->db->commit();
            $transactionStarted = false;
            
            $results[] = ['status' => 'success', 'message' => 'Todas as migrações executadas com sucesso!'];

        } catch (Exception $e) {
            if ($transactionStarted) {
                try {
                    $this->db->rollBack();
                } catch (Exception $rollbackException) {
                }
            }
            $results[] = ['status' => 'error', 'message' => 'Erro na migração: ' . $e->getMessage()];
        }

        return $results;
    }

    private function createCouponsTable(): array
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'cupons'");
            if ($stmt->rowCount() > 0) {
                return ['status' => 'info', 'message' => 'Tabela cupons já existe'];
            }

            $sql = "CREATE TABLE cupons (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) NOT NULL UNIQUE,
                type ENUM('fixed', 'percentage') NOT NULL DEFAULT 'fixed',
                value DECIMAL(10,2) NOT NULL,
                min_order_value DECIMAL(10,2) DEFAULT 0,
                valid_from DATETIME NOT NULL,
                valid_until DATETIME NOT NULL,
                usage_limit INT DEFAULT NULL,
                used_count INT DEFAULT 0,
                active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->db->exec($sql);
            return ['status' => 'success', 'message' => 'Tabela cupons criada com sucesso'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao criar tabela cupons: ' . $e->getMessage()];
        }
    }

    private function createProductVariationsTable(): array
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'product_variations'");
            if ($stmt->rowCount() > 0) {
                return ['status' => 'info', 'message' => 'Tabela product_variations já existe'];
            }

            $sql = "CREATE TABLE product_variations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                sku VARCHAR(100) UNIQUE DEFAULT NULL,
                price_adjustment DECIMAL(10,2) DEFAULT 0,
                attributes JSON DEFAULT NULL,
                active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->db->exec($sql);
            return ['status' => 'success', 'message' => 'Tabela product_variations criada com sucesso'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao criar tabela product_variations: ' . $e->getMessage()];
        }
    }

    private function createStockTable(): array
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'estoque'");
            if ($stmt->rowCount() > 0) {
                return ['status' => 'info', 'message' => 'Tabela estoque já existe'];
            }

            $sql = "CREATE TABLE estoque (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                variation_id INT DEFAULT NULL,
                quantity INT NOT NULL DEFAULT 0,
                reserved_quantity INT DEFAULT 0,
                min_stock INT DEFAULT 5,
                location VARCHAR(100) DEFAULT NULL,
                last_movement DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                UNIQUE KEY unique_product_variation (product_id, variation_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->db->exec($sql);
            return ['status' => 'success', 'message' => 'Tabela estoque criada com sucesso'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao criar tabela estoque: ' . $e->getMessage()];
        }
    }

    private function updateSalesTable(): array
    {
        try {
            $updates = [];
            $columns = [
                'shipping_cost' => 'DECIMAL(10,2) DEFAULT 0',
                'coupon_code' => 'VARCHAR(50) DEFAULT NULL',
                'coupon_discount' => 'DECIMAL(10,2) DEFAULT 0',
                'customer_cep' => 'VARCHAR(9) DEFAULT NULL',
                'customer_address' => 'TEXT DEFAULT NULL',
                'customer_city' => 'VARCHAR(100) DEFAULT NULL',
                'customer_state' => 'VARCHAR(2) DEFAULT NULL'
            ];

            foreach ($columns as $column => $definition) {
                try {
                    $stmt = $this->db->query("SHOW COLUMNS FROM sales LIKE '$column'");
                    if ($stmt->rowCount() > 0) {
                        $updates[] = "Coluna $column já existe";
                        continue;
                    }

                    $sql = "ALTER TABLE sales ADD COLUMN $column $definition";
                    $this->db->exec($sql);
                    $updates[] = "Coluna $column adicionada";
                } catch (Exception $e) {
                    $updates[] = "Erro na coluna $column: " . $e->getMessage();
                }
            }

            return ['status' => 'success', 'message' => 'Tabela sales: ' . implode(', ', $updates)];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao atualizar tabela sales: ' . $e->getMessage()];
        }
    }

    private function migrateExistingStock(): array
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM estoque");
            $count = $stmt->fetch()['count'];

            if ($count > 0) {
                return ['status' => 'info', 'message' => 'Dados de estoque já migrados'];
            }

            $sql = "INSERT INTO estoque (product_id, quantity, min_stock)
                    SELECT id, stock as quantity, min_stock 
                    FROM products 
                    WHERE active = 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $affected = $stmt->rowCount();

            return ['status' => 'success', 'message' => "$affected produtos migrados para tabela estoque"];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao migrar estoque: ' . $e->getMessage()];
        }
    }

    private function insertSampleCoupons(): array
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM cupons");
            $count = $stmt->fetch()['count'];

            if ($count > 0) {
                return ['status' => 'info', 'message' => 'Cupons de exemplo já existem'];
            }

            $coupons = [
                ['DESCONTO10', 'percentage', 10.00, 50.00, 100],
                ['FRETE15', 'fixed', 15.00, 100.00, NULL],
                ['BEMVINDO', 'fixed', 25.00, 150.00, 50]
            ];

            $sql = "INSERT INTO cupons (code, type, value, min_order_value, valid_from, valid_until, usage_limit) 
                    VALUES (?, ?, ?, ?, '2025-01-01 00:00:00', '2025-12-31 23:59:59', ?)";

            $stmt = $this->db->prepare($sql);
            foreach ($coupons as $coupon) {
                $stmt->execute($coupon);
            }

            return ['status' => 'success', 'message' => count($coupons) . ' cupons de exemplo criados'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao criar cupons: ' . $e->getMessage()];
        }
    }

    public function checkTablesExist(): array
    {
        $tables = ['cupons', 'estoque', 'product_variations'];
        $status = [];

        foreach ($tables as $table) {
            try {
                $stmt = $this->db->query("SELECT 1 FROM $table LIMIT 1");
                $status[$table] = 'exists';
            } catch (Exception $e) {
                $status[$table] = 'missing';
            }
        }

        return $status;
    }
}