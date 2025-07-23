<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function group(string $prefix, callable $callback): void
    {
        $groupRouter = new class($this, $prefix) {
            private Router $router;
            private string $prefix;

            public function __construct(Router $router, string $prefix)
            {
                $this->router = $router;
                $this->prefix = rtrim($prefix, '/');
            }

            public function get(string $path, string $handler): void
            {
                $fullPath = $path === '/' ? $this->prefix : $this->prefix . $path;
                $this->router->get($fullPath, $handler);
            }

            public function post(string $path, string $handler): void
            {
                $fullPath = $path === '/' ? $this->prefix : $this->prefix . $path;
                $this->router->post($fullPath, $handler);
            }
        };

        $callback($groupRouter);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        if (isset($_GET['route'])) {
            $uri = '/' . trim($_GET['route'], '/');
        } else {
            $uri = parse_url($uri, PHP_URL_PATH);
            
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $basePath = dirname($scriptName);
            if ($basePath !== '/' && $basePath !== '\\') {
                $uri = str_replace($basePath, '', $uri);
            }
            
            $uri = str_replace('/index.php', '', $uri);
        }
        
        $uri = '/' . ltrim($uri, '/');
        if ($uri === '//' || $uri === '') {
            $uri = '/';
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                $this->callHandler($route['handler'], $uri, $route['path']);
                return;
            }
        }

        http_response_code(404);
        $this->handle404($uri);
    }

    private function matchPath(string $routePath, string $uri): bool
    {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $routePattern = '#^' . $routePattern . '$#';
        return (bool) preg_match($routePattern, $uri);
    }

    private function callHandler(string $handler, string $uri, string $routePath): void
    {
        [$controllerName, $method] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} not found");
        }

        $controller = new $controllerClass($this->container);
        
        if (!method_exists($controller, $method)) {
            throw new \Exception("Method {$method} not found in {$controllerClass}");
        }

        $params = $this->extractParams($routePath, $uri);
        $controller->$method(...$params);
    }

    private function extractParams(string $routePath, string $uri): array
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));
        $params = [];

        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([^}]+)\}/', $part)) {
                $params[] = $uriParts[$index] ?? null;
            }
        }

        return $params;
    }
    
    private function handle404(string $uri): void
    {
        $errorLogService = new \App\Services\ErrorLogService();
        $ticketId = $errorLogService->log404($uri);
        
        if (isset($_SESSION['user_id'])) {
            $content = $this->renderView('errors/404', [
                'requestedUri' => $uri,
                'ticketId' => $ticketId
            ]);
            echo $this->renderView('layout', [
                'title' => 'Página não encontrada - Teste Montink',
                'content' => $content
            ]);
        } else {
            $content = $this->renderView('errors/404', [
                'requestedUri' => $uri,
                'ticketId' => $ticketId
            ]);
            echo $this->renderView('shop-layout', [
                'title' => 'Página não encontrada - Teste Montink',
                'content' => $content
            ]);
        }
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
}
