<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use Exception;

class BackupService
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function createFullBackup(): array
    {
        try {
            $pdo = $this->database->getConnection();
            $backupData = [];
            
            $tables = $this->getAllTables();
            
            $backupData['metadata'] = [
                'created_at' => date('Y-m-d H:i:s'),
                'database_name' => $this->getDatabaseName(),
                'version' => '1.0',
                'tables_count' => count($tables)
            ];
            
            foreach ($tables as $table) {
                $backupData['tables'][$table] = [
                    'structure' => $this->getTableStructure($table),
                    'data' => $this->getTableData($table)
                ];
            }
            
            return [
                'success' => true,
                'backup' => $backupData,
                'size' => $this->calculateBackupSize($backupData)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function exportToSqlFile(array $backupData): array
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = __DIR__ . '/../../storage/backups/' . $filename;
            
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            $sql = $this->generateSqlContent($backupData);
            
            if (file_put_contents($filepath, $sql) === false) {
                throw new Exception('Erro ao escrever arquivo de backup');
            }

            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => filesize($filepath)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function restoreFromSqlFile(string $filepath): array
    {
        try {
            if (!file_exists($filepath)) {
                throw new Exception('Arquivo de backup não encontrado');
            }

            $sql = file_get_contents($filepath);
            if ($sql === false) {
                throw new Exception('Erro ao ler arquivo de backup');
            }

            $pdo = $this->database->getConnection();
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            
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
            
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

            return [
                'success' => true,
                'statements_executed' => $executedCount,
                'message' => 'Backup restaurado com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getAvailableBackups(): array
    {
        $backupsDir = __DIR__ . '/../../storage/backups/';
        $backups = [];
        
        if (!is_dir($backupsDir)) {
            return $backups;
        }
        
        $files = glob($backupsDir . 'backup_*.sql');
        
        foreach ($files as $file) {
            $filename = basename($file);
            $backups[] = [
                'filename' => $filename,
                'filepath' => $file,
                'size' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                'human_size' => $this->formatBytes(filesize($file))
            ];
        }
        
        usort($backups, function($a, $b) {
            return filemtime($b['filepath']) - filemtime($a['filepath']);
        });
        
        return $backups;
    }

    private function getAllTables(): array
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query('SHOW TABLES');
        $tables = [];
        
        while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        return $tables;
    }

    private function getTableStructure(string $table): string
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row['Create Table'] ?? '';
    }

    private function getTableData(string $table): array
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query("SELECT * FROM `{$table}`");
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getDatabaseName(): string
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query('SELECT DATABASE()');
        
        return $stmt->fetchColumn() ?: 'unknown';
    }

    private function calculateBackupSize(array $backupData): int
    {
        return strlen(json_encode($backupData));
    }

    private function generateSqlContent(array $backupData): string
    {
        $sql = "-- Backup gerado automaticamente pelo Teste Montink\n";
        $sql .= "-- Data: " . $backupData['metadata']['created_at'] . "\n";
        $sql .= "-- Database: " . $backupData['metadata']['database_name'] . "\n";
        $sql .= "-- Tabelas: " . $backupData['metadata']['tables_count'] . "\n\n";
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        
        foreach ($backupData['tables'] as $tableName => $tableData) {
            $sql .= "-- Estrutura da tabela `{$tableName}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $tableData['structure'] . ";\n\n";
            
            if (!empty($tableData['data'])) {
                $sql .= "-- Dados da tabela `{$tableName}`\n";
                $sql .= "INSERT INTO `{$tableName}` VALUES\n";
                
                $values = [];
                foreach ($tableData['data'] as $row) {
                    $rowValues = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }
                
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        
        return $sql;
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

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}