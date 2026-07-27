<?php

class PropertyController extends Controller
{
    private array $tiposValidos = ['venta', 'alquiler', 'pozo'];
    private array $estadosValidos = ['disponible', 'alquilada', 'vendida', 'reservada'];

    /**
     * Listado paginado, PÚBLICO (no requiere login).
     * Si el visitante es agente logueado, ve además accesos de gestión sobre sus propias propiedades.
     * Si es admin, ve accesos de gestión sobre todas.
     */
    public function index(): void
    {
        $propertyModel = new Property();
        $filters = [];

        if (Auth::hasRole('agente')) {
            $filters['agente_id'] = Auth::id();
        }

        if (!empty($_GET['tipo']) && in_array($_GET['tipo'], $this->tiposValidos, true)) {
            $filters['tipo'] = $_GET['tipo'];
        }
        if (!empty($_GET['zona'])) {
            $filters['zona'] = clean_string($_GET['zona']);
        }
        if (!empty($_GET['precio_min'])) {
            $filters['precio_min'] = clean_float($_GET['precio_min']);
        }
        if (!empty($_GET['precio_max'])) {
            $filters['precio_max'] = clean_float($_GET['precio_max']);
        }
        if (!empty($_GET['dormitorios'])) {
            $filters['dormitorios'] = clean_int($_GET['dormitorios']);
        }
        $ordenesValidos = ['recientes', 'precio_asc', 'precio_desc'];
        $filters['orden'] = in_array($_GET['orden'] ?? '', $ordenesValidos, true) ? $_GET['orden'] : 'recientes';

        $total = $propertyModel->count($filters);
        $pagination = paginate($total);
        $properties = $propertyModel->paginated($pagination['perPage'], $pagination['offset'], $filters);

        $this->render('properties/index', [
            'properties' => $properties,
            'pagination' => $pagination,
            'filters'    => $filters,
        ]);
    }

    /** Detalle de una propiedad, PÚBLICO (no requiere login) */
    /** Página exclusiva del cliente: solo las propiedades asociadas a su cuenta */
    public function misPropiedades(): void
    {
        Auth::requireRole('cliente');

        $propertyModel = new Property();
        $propiedades = $propertyModel->misPropiedades(Auth::id());

        $this->render('properties/mis_propiedades', ['propiedades' => $propiedades]);
    }

    public function show(string $id): void
    {
        $property = $this->findOrFail((int) $id);
        $this->render('properties/show', ['property' => $property]);
    }

    public function showCreate(): void
    {
        Auth::requireRole('administrador', 'agente');

        $userModel = new User();
        $this->render('properties/create', [
            'agentes'  => Auth::hasRole('administrador') ? $userModel->agentes() : [],
            'clientes' => $userModel->clientes(),
            'csrf'     => csrf_token(),
            'old'      => [],
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('administrador', 'agente');

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/propiedades/nueva');
        }

        [$data, $errors] = $this->validate($_POST);

        // El agente siempre carga a su propio nombre; el admin puede elegir cualquier agente
        $data['agente_id'] = Auth::hasRole('agente') ? Auth::id() : clean_int($_POST['agente_id'] ?? 0);

        if ($data['agente_id'] <= 0) {
            $errors[] = 'Debés seleccionar un agente responsable.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/propiedades/nueva');
        }

        $data['imagen'] = $this->handleUpload();

        $propertyModel = new Property();
        $propertyModel->create($data);

        set_flash('success', 'Propiedad creada correctamente.');
        redirect('/propiedades');
    }

    public function showEdit(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $property = $this->findOrFail((int) $id);
        $this->authorizeManage($property);

        $userModel = new User();
        $this->render('properties/edit', [
            'property' => $property,
            'agentes'  => Auth::hasRole('administrador') ? $userModel->agentes() : [],
            'clientes' => $userModel->clientes(),
            'csrf'     => csrf_token(),
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $property = $this->findOrFail((int) $id);
        $this->authorizeManage($property);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/propiedades/' . $property['id'] . '/editar');
        }

        [$data, $errors] = $this->validate($_POST);

        // El agente no puede reasignar la propiedad a otro agente
        $data['agente_id'] = Auth::hasRole('administrador')
            ? clean_int($_POST['agente_id'] ?? 0)
            : $property['agente_id'];

        if ($data['agente_id'] <= 0) {
            $errors[] = 'Debés seleccionar un agente responsable.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/propiedades/' . $property['id'] . '/editar');
        }

        $nuevaImagen = $this->handleUpload();
        if ($nuevaImagen) {
            $data['imagen'] = $nuevaImagen;
        }

        $propertyModel = new Property();
        $propertyModel->update((int) $property['id'], $data);

        set_flash('success', 'Propiedad actualizada correctamente.');
        redirect('/propiedades');
    }

    public function destroy(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $property = $this->findOrFail((int) $id);
        $this->authorizeManage($property);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/propiedades');
        }

        if (!empty($property['imagen'])) {
            @unlink(UPLOADS_DIR . '/' . $property['imagen']);
        }

        $propertyModel = new Property();
        $propertyModel->delete((int) $property['id']);

        set_flash('success', 'Propiedad eliminada.');
        redirect('/propiedades');
    }

    // ---------------------------------------------------------------
    // Helpers privados
    // ---------------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $propertyModel = new Property();
        $property = $propertyModel->find($id);
        if (!$property) {
            http_response_code(404);
            require VIEWS_DIR . '/errors/404.php';
            exit;
        }
        return $property;
    }

    /** Un agente solo puede editar/eliminar SUS propias propiedades, ni pegando la URL de otra */
    private function authorizeManage(array $property): void
    {
        if (Auth::hasRole('agente') && (int) $property['agente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }

    /** Valida y sanea los campos del formulario de propiedad. Devuelve [data, errores] */
    private function validate(array $input): array
    {
        $errors = [];

        $titulo = clean_string($input['titulo'] ?? '');
        $descripcion = clean_string($input['descripcion'] ?? '');
        $tipo = $input['tipo'] ?? '';
        $precio = clean_float($input['precio'] ?? 0);
        $zona = clean_string($input['zona'] ?? '');
        $direccion = clean_string($input['direccion'] ?? '');
        $dormitorios = clean_int($input['dormitorios'] ?? 0, 0);
        $banos = clean_int($input['banos'] ?? 0, 0);
        $estado = $input['estado'] ?? 'disponible';
        $propietarioId = clean_int($input['propietario_id'] ?? 0, 0);

        if ($titulo === '' || mb_strlen($titulo) < 5) {
            $errors[] = 'El título debe tener al menos 5 caracteres.';
        }
        if ($descripcion === '' || mb_strlen($descripcion) < 10) {
            $errors[] = 'La descripción debe tener al menos 10 caracteres.';
        }
        if (!in_array($tipo, $this->tiposValidos, true)) {
            $errors[] = 'El tipo de propiedad no es válido.';
        }
        if ($precio <= 0) {
            $errors[] = 'El precio debe ser mayor a cero.';
        }
        if ($zona === '') {
            $errors[] = 'La zona es obligatoria.';
        }
        if ($direccion === '') {
            $errors[] = 'La dirección es obligatoria.';
        }
        if (!in_array($estado, $this->estadosValidos, true)) {
            $errors[] = 'El estado no es válido.';
        }

        $data = [
            'titulo'         => $titulo,
            'descripcion'    => $descripcion,
            'tipo'           => $tipo,
            'precio'         => $precio,
            'zona'           => $zona,
            'direccion'      => $direccion,
            'dormitorios'    => $dormitorios,
            'banos'          => $banos,
            'estado'         => $estado,
            'propietario_id' => $propietarioId ?: null,
        ];

        return [$data, $errors];
    }

    /** Sube una imagen de propiedad si vino en el request. Devuelve el nombre de archivo o null. */
    private function handleUpload(): ?string
    {
        if (empty($_FILES['imagen']['name']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            set_flash('error', 'Hubo un problema al subir la imagen.');
            return null;
        }

        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($_FILES['imagen']['tmp_name']);

        if (!isset($permitidos[$mime])) {
            set_flash('error', 'La imagen debe ser JPG, PNG o WEBP.');
            return null;
        }

        if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            set_flash('error', 'La imagen no puede pesar más de 5MB.');
            return null;
        }

        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $permitidos[$mime];
        move_uploaded_file($_FILES['imagen']['tmp_name'], UPLOADS_DIR . '/' . $nombreArchivo);

        return $nombreArchivo;
    }
}
