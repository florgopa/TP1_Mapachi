<?php

class PageController extends Controller
{
    public function nosotros(): void
    {
        $this->render('pages/nosotros');
    }

    public function showContacto(): void
    {
        $this->render('pages/contacto', ['csrf' => csrf_token()]);
    }

    public function enviarContacto(): void
    {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/#contacto');
        }

        $nombre = clean_string($_POST['nombre'] ?? '');
        $email = clean_email($_POST['email'] ?? '');
        $telefono = clean_string($_POST['telefono'] ?? '');
        $mensaje = clean_string($_POST['mensaje'] ?? '');

        if ($nombre === '' || !$email || $mensaje === '') {
            set_flash('error', 'Completá nombre, email y mensaje correctamente.');
            redirect('/#contacto');
        }

        $messageModel = new Message();
        $messageModel->create([
            'nombre'   => $nombre,
            'email'    => $email,
            'telefono' => $telefono,
            'mensaje'  => $mensaje,
        ]);

        set_flash('success', '¡Gracias! Recibimos tu mensaje y te vamos a contactar a la brevedad.');
        redirect('/#contacto');
    }

    /** Bandeja de mensajes de contacto, solo para administradores */
    public function mensajes(): void
    {
        Auth::requireRole('administrador');

        $messageModel = new Message();
        $total = $messageModel->count();
        $pagination = paginate($total);
        $mensajes = $messageModel->paginated($pagination['perPage'], $pagination['offset']);

        $this->render('pages/mensajes', ['mensajes' => $mensajes, 'pagination' => $pagination]);
    }
}
