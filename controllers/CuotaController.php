<?php

class CuotaController extends Controller
{
    /** El inquilino sube el comprobante de esa cuota puntual */
    public function subirComprobante(string $id): void
    {
        Auth::requireRole('cliente');
        $cuota = $this->findOrFail((int) $id);

        if ((int) $cuota['inquilino_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/contratos/' . $cuota['contrato_id']);
        }

        $archivo = $this->handleUpload();
        if (!$archivo) {
            set_flash('error', 'Subí un comprobante en PDF, JPG o PNG (máx. 5MB).');
            redirect('/contratos/' . $cuota['contrato_id']);
        }

        $cuotaModel = new Cuota();
        $cuotaModel->subirComprobante((int) $cuota['id'], $archivo);

        set_flash('success', 'Comprobante subido. La cuota quedó marcada como pagada.');
        redirect('/contratos/' . $cuota['contrato_id']);
    }

    /** Admin/agente confirma, revierte o marca vencida una cuota */
    public function actualizarEstado(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $cuota = $this->findOrFail((int) $id);
        $this->authorizeManage($cuota);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/contratos/' . $cuota['contrato_id']);
        }

        $estado = $_POST['estado'] ?? '';
        if (!in_array($estado, ['pendiente', 'pagado', 'vencido'], true)) {
            set_flash('error', 'Estado no válido.');
            redirect('/contratos/' . $cuota['contrato_id']);
        }

        $cuotaModel = new Cuota();
        $cuotaModel->actualizarEstado((int) $cuota['id'], $estado);

        set_flash('success', 'Cuota actualizada.');
        redirect('/contratos/' . $cuota['contrato_id']);
    }

    /** Admin/agente carga el monto a mano cuando la cuota está "a convenir" */
    public function cargarMonto(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $cuota = $this->findOrFail((int) $id);
        $this->authorizeManage($cuota);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/contratos/' . $cuota['contrato_id']);
        }

        $monto = clean_float($_POST['monto'] ?? 0);
        if ($monto <= 0) {
            set_flash('error', 'Ingresá un monto válido.');
            redirect('/contratos/' . $cuota['contrato_id']);
        }

        $cuotaModel = new Cuota();
        $cuotaModel->cargarMontoManual((int) $cuota['id'], $monto);

        set_flash('success', 'Monto cargado.');
        redirect('/contratos/' . $cuota['contrato_id']);
    }

    private function findOrFail(int $id): array
    {
        $cuotaModel = new Cuota();
        $cuota = $cuotaModel->find($id);
        if (!$cuota) {
            http_response_code(404);
            require VIEWS_DIR . '/errors/404.php';
            exit;
        }
        return $cuota;
    }

    private function authorizeManage(array $cuota): void
    {
        if (Auth::hasRole('agente') && (int) $cuota['agente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }

    private function handleUpload(): ?string
    {
        if (empty($_FILES['archivo']['name']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
        $mime = mime_content_type($_FILES['archivo']['tmp_name']);

        if (!isset($permitidos[$mime]) || $_FILES['archivo']['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $permitidos[$mime];
        move_uploaded_file($_FILES['archivo']['tmp_name'], UPLOADS_DIR . '/' . $nombreArchivo);

        return $nombreArchivo;
    }
}
