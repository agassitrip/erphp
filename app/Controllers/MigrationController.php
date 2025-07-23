<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\MigrationService;

class MigrationController extends BaseController
{
    public function run(): void
    {
        if (!$this->isDevelopment()) {
            http_response_code(403);
            echo "Migrações só podem ser executadas em ambiente de desenvolvimento";
            return;
        }

        $migrationService = new MigrationService($this->container->get('database'));
        $results = $migrationService->runMigrations();

        $this->renderMigrationResults($results);
    }

    public function status(): void
    {
        $migrationService = new MigrationService($this->container->get('database'));
        $tablesStatus = $migrationService->checkTablesExist();

        $this->renderTablesStatus($tablesStatus);
    }

    private function isDevelopment(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        return in_array($host, ['localhost', '127.0.0.1', 'localhost:8000']) || 
               strpos($host, 'localhost:') === 0;
    }

    private function renderMigrationResults(array $results): void
    {
        echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Migrações - Teste Montink</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-lg-8'>
                <div class='card shadow'>
                    <div class='card-header bg-primary text-white'>
                        <h3 class='mb-0'><i class='bi bi-database-gear'></i> Resultado das Migrações</h3>
                    </div>
                    <div class='card-body'>";

        foreach ($results as $result) {
            $alertClass = match($result['status']) {
                'success' => 'alert-success',
                'error' => 'alert-danger',
                'info' => 'alert-info',
                default => 'alert-secondary'
            };

            $icon = match($result['status']) {
                'success' => 'bi-check-circle',
                'error' => 'bi-x-circle',
                'info' => 'bi-info-circle',
                default => 'bi-circle'
            };

            echo "<div class='alert $alertClass'>
                    <i class='bi $icon'></i> {$result['message']}
                  </div>";
        }

        echo "          <div class='d-grid gap-2 mt-4'>
                        <a href='/shop' class='btn btn-success'>
                            <i class='bi bi-shop'></i> Ir para a Loja
                        </a>
                        <a href='/' class='btn btn-primary'>
                            <i class='bi bi-house'></i> Voltar ao Dashboard
                        </a>
                        <a href='/migrate/status' class='btn btn-outline-info'>
                            <i class='bi bi-info-circle'></i> Ver Status das Tabelas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
    }

    private function renderTablesStatus(array $tablesStatus): void
    {
        echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Status das Tabelas - Teste Montink</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-lg-6'>
                <div class='card shadow'>
                    <div class='card-header bg-info text-white'>
                        <h3 class='mb-0'><i class='bi bi-table'></i> Status das Tabelas</h3>
                    </div>
                    <div class='card-body'>
                        <div class='table-responsive'>
                            <table class='table table-striped'>
                                <thead>
                                    <tr>
                                        <th>Tabela</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>";

        foreach ($tablesStatus as $table => $status) {
            $badge = $status === 'exists' ? 
                "<span class='badge bg-success'><i class='bi bi-check'></i> Existe</span>" :
                "<span class='badge bg-danger'><i class='bi bi-x'></i> Não existe</span>";

            echo "<tr>
                    <td><code>$table</code></td>
                    <td>$badge</td>
                  </tr>";
        }

        $allExist = !in_array('missing', array_values($tablesStatus));

        echo "              </tbody>
                            </table>
                        </div>";

        if (!$allExist) {
            echo "<div class='alert alert-warning'>
                    <i class='bi bi-exclamation-triangle'></i>
                    <strong>Algumas tabelas estão faltando!</strong> Execute as migrações.
                  </div>
                  <div class='d-grid'>
                    <a href='/migrate/run' class='btn btn-warning'>
                        <i class='bi bi-database-gear'></i> Executar Migrações
                    </a>
                  </div>";
        } else {
            echo "<div class='alert alert-success'>
                    <i class='bi bi-check-circle'></i>
                    <strong>Todas as tabelas estão prontas!</strong>
                  </div>
                  <div class='d-grid'>
                    <a href='/shop' class='btn btn-success'>
                        <i class='bi bi-shop'></i> Ir para a Loja
                    </a>
                  </div>";
        }

        echo "      </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
    }
}