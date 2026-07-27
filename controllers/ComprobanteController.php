<?php

class ComprobanteController extends Controller
{
    /** Cliente ve los suyos; agente ve los de sus propiedades; admin ve todos */
    public function index(): void
    {
        Auth::requireLogin();

        $comprobanteModel = new Comprobante();
        $filters = [];

        if (Auth::hasRole('cliente')) {
            $filters['cliente_id'] = Auth::id();
        } elseif (Auth::hasRole('agente')) {
            $filters['agente_id'] = Auth::id();
        }

        $total = $comprobanteModel->count($filters);
        $pagination = paginate($total);
        $comprobantes = $comprobanteModel->paginated($pagination['perPage'], $pagination['offset'], $filters);

        $this->render('comprobantes/index', ['comprobantes' => $comprobantes, 'pagination' => $pagination]);
    }

    public function showCreate(): void
    {
        Auth::requireRole('cliente');

        $propertyModel = new Property();
        $misPropiedades = $propertyModel->misPropiedades(Auth::id());

        if (empty($misPropiedades)) {
            set_flash('error', 'No tenés propiedades asociadas a tu cuenta todavía.');
            redirect('/comprobantes');
        }

        $this->render('comprobantes/create', ['propiedades' => $misPropiedades, 'csrf' => csrf_token()]);
    }

    public function store(): void
    {
        Auth::requireRole('cliente');

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/comprobantes/nuevo');
        }

        $propertyId = clean_int($_POST['property_id'] ?? 0);
        $monto = clean_float($_POST['monto'] ?? 0);

        $errors = [];

        $propertyModel = new Property();
        $property = $propertyModel->find($propertyId);
        if (!$property || (int) $property['propietario_id'] !== Auth::id()) {
            $errors[] = 'La propiedad seleccionada no es válida.';
        }

        $archivo = $this->handleUpload();
        if (!$archivo) {
            $errors[] = 'Subí un comprobante en PDF, JPG o PNG.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/comprobantes/nuevo');
        }

        $comprobanteModel = new Comprobante();
        $comprobanteModel->create([
            'property_id' => $propertyId,
            'cliente_id'  => Auth::id(),
            'archivo'     => $archivo,
            'monto'       => $monto,
        ]);

        set_flash('success', 'Comprobante subido correctamente. Va a ser revisado a la brevedad.');
        redirect('/comprobantes');
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $comprobante = $this->findOrFail((int) $id);
        $this->authorizeView($comprobante);

        $this->render('comprobantes/show', ['comprobante' => $comprobante, 'csrf' => csrf_token()]);
    }

    /** Admin/agente aprueba o rechaza */
    public function updateEstado(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $comprobante = $this->findOrFail((int) $id);
        $this->authorizeManage($comprobante);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/comprobantes/' . $comprobante['id']);
        }

        $estado = $_POST['estado'] ?? '';
        $nota = clean_string($_POST['nota_admin'] ?? '');

        if (!in_array($estado, ['pendiente', 'aprobado', 'rechazado'], true)) {
            set_flash('error', 'Estado no válido.');
            redirect('/comprobantes/' . $comprobante['id']);
        }

        $comprobanteModel = new Comprobante();
        $comprobanteModel->updateEstado((int) $comprobante['id'], $estado, $nota ?: null);

        set_flash('success', 'Comprobante actualizado.');
        redirect('/comprobantes/' . $comprobante['id']);
    }

    private function findOrFail(int $id): array
    {
        $comprobanteModel = new Comprobante();
        $comprobante = $comprobanteModel->find($id);
        if (!$comprobante) {
            http_response_code(404);
            require VIEWS_DIR . '/errors/404.php';
            exit;
        }
        return $comprobante;
    }

    private function authorizeView(array $comprobante): void
    {
        if (Auth::hasRole('cliente') && (int) $comprobante['cliente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
        if (Auth::hasRole('agente') && (int) $comprobante['agente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }

    private function authorizeManage(array $comprobante): void
    {
        if (Auth::hasRole('agente') && (int) $comprobante['agente_id'] !== Auth::id()) {
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

        $permitidos = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'application/pdf' => 'pdf',
        ];
        $mime = mime_content_type($_FILES['archivo']['tmp_name']);

        if (!isset($permitidos[$mime])) {
            return null;
        }
        if ($_FILES['archivo']['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $permitidos[$mime];
        move_uploaded_file($_FILES['archivo']['tmp_name'], UPLOADS_DIR . '/' . $nombreArchivo);

        return $nombreArchivo;
    }
}
