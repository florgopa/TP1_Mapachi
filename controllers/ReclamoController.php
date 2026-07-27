<?php

class ReclamoController extends Controller
{
    private array $estadosValidos = ['abierto', 'en_proceso', 'resuelto'];

    /** Cliente ve los suyos; agente ve los de sus propiedades; admin ve todos */
    public function index(): void
    {
        Auth::requireLogin();

        $reclamoModel = new Reclamo();
        $filters = [];

        if (Auth::hasRole('cliente')) {
            $filters['cliente_id'] = Auth::id();
        } elseif (Auth::hasRole('agente')) {
            $filters['agente_id'] = Auth::id();
        }
        // administrador: sin filtro, ve todos

        $total = $reclamoModel->count($filters);
        $pagination = paginate($total);
        $reclamos = $reclamoModel->paginated($pagination['perPage'], $pagination['offset'], $filters);

        $this->render('reclamos/index', ['reclamos' => $reclamos, 'pagination' => $pagination]);
    }

    public function showCreate(): void
    {
        Auth::requireRole('cliente');

        $propertyModel = new Property();
        $misPropiedades = $propertyModel->misPropiedades(Auth::id());

        if (empty($misPropiedades)) {
            set_flash('error', 'No tenés propiedades asociadas a tu cuenta todavía, por lo que no podés levantar un reclamo.');
            redirect('/reclamos');
        }

        $this->render('reclamos/create', ['propiedades' => $misPropiedades, 'csrf' => csrf_token()]);
    }

    public function store(): void
    {
        Auth::requireRole('cliente');

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/reclamos/nuevo');
        }

        $propertyId = clean_int($_POST['property_id'] ?? 0);
        $titulo = clean_string($_POST['titulo'] ?? '');
        $descripcion = clean_string($_POST['descripcion'] ?? '');

        $errors = [];
        if ($titulo === '' || mb_strlen($titulo) < 5) {
            $errors[] = 'El título debe tener al menos 5 caracteres.';
        }
        if ($descripcion === '' || mb_strlen($descripcion) < 10) {
            $errors[] = 'Contanos un poco más el problema (mínimo 10 caracteres).';
        }

        // Verificamos que la propiedad elegida REALMENTE sea del cliente logueado,
        // para que no pueda levantar un reclamo sobre una propiedad ajena manipulando el form.
        $propertyModel = new Property();
        $property = $propertyModel->find($propertyId);
        if (!$property || (int) $property['propietario_id'] !== Auth::id()) {
            $errors[] = 'La propiedad seleccionada no es válida.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/reclamos/nuevo');
        }

        $reclamoModel = new Reclamo();
        $reclamoModel->create([
            'property_id' => $propertyId,
            'cliente_id'  => Auth::id(),
            'titulo'      => $titulo,
            'descripcion' => $descripcion,
        ]);

        set_flash('success', 'Reclamo enviado. Te vamos a responder a la brevedad.');
        redirect('/reclamos');
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $reclamo = $this->findOrFail((int) $id);
        $this->authorizeView($reclamo);

        $this->render('reclamos/show', ['reclamo' => $reclamo, 'csrf' => csrf_token()]);
    }

    /** Admin/agente responde y cambia el estado */
    public function updateEstado(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $reclamo = $this->findOrFail((int) $id);
        $this->authorizeManage($reclamo);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/reclamos/' . $reclamo['id']);
        }

        $estado = $_POST['estado'] ?? '';
        $respuesta = clean_string($_POST['respuesta'] ?? '');

        if (!in_array($estado, $this->estadosValidos, true)) {
            set_flash('error', 'Estado no válido.');
            redirect('/reclamos/' . $reclamo['id']);
        }

        $reclamoModel = new Reclamo();
        $reclamoModel->updateEstado((int) $reclamo['id'], $estado, $respuesta ?: null);

        set_flash('success', 'Reclamo actualizado.');
        redirect('/reclamos/' . $reclamo['id']);
    }

    private function findOrFail(int $id): array
    {
        $reclamoModel = new Reclamo();
        $reclamo = $reclamoModel->find($id);
        if (!$reclamo) {
            http_response_code(404);
            require VIEWS_DIR . '/errors/404.php';
            exit;
        }
        return $reclamo;
    }

    private function authorizeView(array $reclamo): void
    {
        if (Auth::hasRole('cliente') && (int) $reclamo['cliente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
        if (Auth::hasRole('agente') && (int) $reclamo['agente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }

    private function authorizeManage(array $reclamo): void
    {
        if (Auth::hasRole('agente') && (int) $reclamo['agente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }
}
