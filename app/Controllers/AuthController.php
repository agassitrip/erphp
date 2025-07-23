<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\AuthService;
use App\Exceptions\BaseException;
use App\Helpers\RedirectHelper;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->authService = $container->get('auth');
    }

    public function showLogin(): void
    {
        if ($this->authService->isLoggedIn()) {
            $this->redirect(RedirectHelper::getHomeUrl());
        }
        $this->view('auth/login');
    }

    public function login(): void
    {
        if (!$this->isPost()) {
            $this->showLogin();
            return;
        }

        try {
            $this->authService->login($_POST['email'] ?? '', $_POST['password'] ?? '');
            $this->flashSuccess('Login realizado com sucesso!');
            $this->redirect(RedirectHelper::getHomeUrl());
        } catch (BaseException $e) {
            $this->flashError($e->getMessage());
            $this->showLogin();
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->flashSuccess('Logout realizado com sucesso!');
        $this->redirect('/login');
    }
}
