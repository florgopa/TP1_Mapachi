<?php

class ProfileController extends Controller
{
    public function show(): void
    {
        Auth::requireLogin();
        $userModel = new User();
        $user = $userModel->findById(Auth::id());

        $this->renderDashboard('profile/show', ['user' => $user, 'csrf' => csrf_token()]);
    }

    public function update(): void
    {
        Auth::requireLogin();

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/perfil');
        }

        $nombre = clean_string($_POST['nombre'] ?? '');
        $email = clean_email($_POST['email'] ?? '');
        $telefono = clean_string($_POST['telefono'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        $errors = [];
        if ($nombre === '' || mb_strlen($nombre) < 3) {
            $errors[] = 'El nombre debe tener al menos 3 caracteres.';
        }
        if (!$email) {
            $errors[] = 'El email no es válido.';
        }
        if ($password !== '' && mb_strlen($password) < 6) {
            $errors[] = 'Si vas a cambiar la contraseña, debe tener al menos 6 caracteres.';
        }
        if ($password !== '' && $password !== $passwordConfirm) {
            $errors[] = 'Las contraseñas nuevas no coinciden.';
        }

        $userModel = new User();
        if (!$errors && $userModel->emailExists($email, Auth::id())) {
            $errors[] = 'Ya existe otro usuario con ese email.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/perfil');
        }

        $current = $userModel->findById(Auth::id());
        $userModel->update(Auth::id(), [
            'nombre'   => $nombre,
            'email'    => $email,
            'telefono' => $telefono,
            'role_id'  => (int) $current['role_id'], // el usuario no puede cambiarse su propio rol
            'activo'   => (int) $current['activo'],
            'password' => $password,
        ]);

        // Refrescamos los datos de sesión (nombre puede haber cambiado)
        $updated = $userModel->findById(Auth::id());
        Auth::login($updated);

        set_flash('success', 'Tus datos se actualizaron correctamente.');
        redirect('/perfil');
    }
}
