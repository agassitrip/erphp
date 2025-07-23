<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\UserService;
use App\DTOs\UserDTO;
use App\Exceptions\ValidationException;
use App\Exceptions\DuplicateException;

class RegisterController extends BaseController
{
    private UserService $userService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->userService = new UserService($this->container->get('database'));
    }

    public function showRegister(): void
    {
        $this->renderPublic('auth/register', [
            'title' => 'Cadastre-se - Teste Montink'
        ]);
    }

    public function register(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/register');
            return;
        }

        try {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                throw new ValidationException('Todos os campos são obrigatórios');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('E-mail inválido');
            }

            if (strlen($password) < 6) {
                throw new ValidationException('Senha deve ter pelo menos 6 caracteres');
            }

            if ($password !== $confirmPassword) {
                throw new ValidationException('Senhas não conferem');
            }

            $userDTO = new UserDTO(
                name: $name,
                email: $email,
                password: $password,
                role: 'user'
            );

            $userId = $this->userService->create($userDTO);

            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';

            $_SESSION['flash_success'] = 'Cadastro realizado com sucesso! Bem-vindo ao Teste Montink!';
            $this->redirect('/shop');

        } catch (ValidationException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/register');
        } catch (DuplicateException $e) {
            $_SESSION['flash_error'] = 'Este e-mail já está cadastrado. Faça login.';
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/register');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erro ao criar conta: ' . $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/register');
        }
    }

    protected function renderPublic(string $view, array $data = []): void
    {
        $content = $this->renderView($view, $data);
        echo $this->renderView('shop-layout', array_merge($data, ['content' => $content]));
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