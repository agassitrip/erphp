<?php

declare(strict_types=1);


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/bootstrap.php';

use App\Core\Router;
use App\Core\Container;
use App\Services\FirstRunService;

session_start();

$firstRunService = new FirstRunService();
if ($firstRunService->isFirstRun()) {
    $currentUri = $_SERVER['REQUEST_URI'];
    $isFirstRunRoute = strpos($currentUri, '/first-run') !== false;
    
    if (!$isFirstRunRoute) {
        header('Location: /first-run');
        exit;
    }
}

$container = Container::getInstance();
$router = new Router($container);

require_once __DIR__ . '/../config/routes.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
