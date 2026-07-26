<?php

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $this->renderPlain('auth/login', ['csrf' => csrf_token()]);
    }

    public function login(): void
    {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/login');
        }

        $email = clean_email($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if (!$email || $password === '') {
            set_flash('error', 'Ingresá un email válido y tu contraseña.');
            redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            set_flash('error', 'Email o contraseña incorrectos.');
            redirect('/login');
        }

        Auth::login($user);
        set_flash('success', 'Bienvenido/a, ' . $user['nombre'] . '.');
        redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
