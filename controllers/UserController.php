<?php

class UserController extends Controller
{
    private array $rolesValidos = [1 => 'administrador', 2 => 'agente', 3 => 'cliente'];

    public function index(): void
    {
        Auth::requireRole('administrador');
        $userModel = new User();
        $this->render('users/index', ['users' => $userModel->all()]);
    }

    public function showCreate(): void
    {
        Auth::requireRole('administrador');
        $this->render('users/create', ['csrf' => csrf_token()]);
    }

    public function store(): void
    {
        Auth::requireRole('administrador');

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/usuarios/nuevo');
        }

        [$data, $errors] = $this->validate($_POST, isCreate: true);

        $userModel = new User();
        if (!$errors && $userModel->emailExists($data['email'])) {
            $errors[] = 'Ya existe un usuario con ese email.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/usuarios/nuevo');
        }

        $userModel->create($data);
        set_flash('success', 'Usuario creado correctamente.');
        redirect('/usuarios');
    }

    public function showEdit(string $id): void
    {
        Auth::requireRole('administrador');
        $user = $this->findOrFail((int) $id);
        $this->render('users/edit', ['user' => $user, 'csrf' => csrf_token()]);
    }

    public function update(string $id): void
    {
        Auth::requireRole('administrador');
        $user = $this->findOrFail((int) $id);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/usuarios/' . $user['id'] . '/editar');
        }

        [$data, $errors] = $this->validate($_POST, isCreate: false);

        $userModel = new User();
        if (!$errors && $userModel->emailExists($data['email'], (int) $user['id'])) {
            $errors[] = 'Ya existe otro usuario con ese email.';
        }

        // Un admin no puede quitarse a sí mismo el rol de administrador ni desactivarse (evita quedar bloqueado)
        if ((int) $user['id'] === Auth::id() && ($data['role_id'] !== 1 || $data['activo'] !== 1)) {
            $errors[] = 'No podés quitarte tu propio rol de administrador ni desactivar tu cuenta.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/usuarios/' . $user['id'] . '/editar');
        }

        $userModel->update((int) $user['id'], $data);
        set_flash('success', 'Usuario actualizado correctamente.');
        redirect('/usuarios');
    }

    public function destroy(string $id): void
    {
        Auth::requireRole('administrador');
        $user = $this->findOrFail((int) $id);

        if ((int) $user['id'] === Auth::id()) {
            set_flash('error', 'No podés eliminar tu propia cuenta.');
            redirect('/usuarios');
        }

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/usuarios');
        }

        $userModel = new User();
        $userModel->delete((int) $user['id']);
        set_flash('success', 'Usuario eliminado.');
        redirect('/usuarios');
    }

    private function findOrFail(int $id): array
    {
        $userModel = new User();
        $user = $userModel->findById($id);
        if (!$user) {
            http_response_code(404);
            require VIEWS_DIR . '/errors/404.php';
            exit;
        }
        return $user;
    }

    private function validate(array $input, bool $isCreate): array
    {
        $errors = [];

        $nombre = clean_string($input['nombre'] ?? '');
        $email = clean_email($input['email'] ?? '');
        $telefono = clean_string($input['telefono'] ?? '');
        $roleId = clean_int($input['role_id'] ?? 0, 0);
        $activo = isset($input['activo']) ? 1 : 0;
        $password = (string) ($input['password'] ?? '');

        if ($nombre === '' || mb_strlen($nombre) < 3) {
            $errors[] = 'El nombre debe tener al menos 3 caracteres.';
        }
        if (!$email) {
            $errors[] = 'El email no es válido.';
        }
        if (!isset($this->rolesValidos[$roleId])) {
            $errors[] = 'Seleccioná un rol válido.';
        }
        if ($isCreate && mb_strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if (!$isCreate && $password !== '' && mb_strlen($password) < 6) {
            $errors[] = 'Si vas a cambiar la contraseña, debe tener al menos 6 caracteres.';
        }

        $data = [
            'nombre'   => $nombre,
            'email'    => $email ?: '',
            'telefono' => $telefono,
            'role_id'  => $roleId,
            'activo'   => $activo,
            'password' => $password,
        ];

        return [$data, $errors];
    }
}
