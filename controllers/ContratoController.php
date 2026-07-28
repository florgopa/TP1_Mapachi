<?php

class ContratoController extends Controller
{
    /** Admin ve todos, agente ve los de sus propiedades, inquilino/propietario ven los suyos */
    public function index(): void
    {
        Auth::requireLogin();

        $contratoModel = new Contrato();
        $filters = [];

        if (Auth::hasRole('agente')) {
            $filters['agente_id'] = Auth::id();
        } elseif (Auth::hasRole('cliente')) {
            // Un cliente puede ser inquilino en algunos contratos y propietario en otros;
            // mostramos ambos casos y los distinguimos en la vista.
            $contratoModel2 = new Contrato();
            $comoInquilino = $contratoModel->list(['inquilino_id' => Auth::id()]);
            $comoPropietario = $contratoModel2->list(['propietario_id' => Auth::id()]);
            $this->renderDashboard('contratos/index', [
                'comoInquilino'   => $comoInquilino,
                'comoPropietario' => $comoPropietario,
            ]);
            return;
        }

        $contratos = $contratoModel->list($filters);
        $this->renderDashboard('contratos/index', ['contratos' => $contratos]);
    }

    public function showCreate(): void
    {
        Auth::requireRole('administrador', 'agente');

        $propertyModel = new Property();
        $userModel = new User();

        $this->renderDashboard('contratos/create', [
            'propiedades' => $propertyModel->paginated(100, 0, ['tipo' => 'alquiler']),
            'clientes'    => $userModel->clientes(),
            'csrf'        => csrf_token(),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('administrador', 'agente');

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/contratos/nuevo');
        }

        $propertyId = clean_int($_POST['property_id'] ?? 0);
        $inquilinoId = clean_int($_POST['inquilino_id'] ?? 0);
        $propietarioId = clean_int($_POST['propietario_id'] ?? 0);
        $montoInicial = clean_float($_POST['monto_inicial'] ?? 0);
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $duracionMeses = clean_int($_POST['duracion_meses'] ?? 0);
        $frecuenciaAjuste = clean_int($_POST['frecuencia_ajuste_meses'] ?? 0);

        $errors = [];

        $propertyModel = new Property();
        $property = $propertyModel->find($propertyId);
        if (!$property || $property['tipo'] !== 'alquiler') {
            $errors[] = 'Seleccioná una propiedad de alquiler válida.';
        }
        if ($inquilinoId <= 0) {
            $errors[] = 'Seleccioná el inquilino.';
        } elseif ($propertyModel->inquilinoTieneOtraPropiedad($inquilinoId, $propertyId)) {
            $errors[] = 'Ese inquilino ya está asociado a otra propiedad. En este sistema cada inquilino alquila una sola propiedad a la vez.';
        }
        if ($propietarioId <= 0) {
            $errors[] = 'Seleccioná el propietario.';
        }
        if ($inquilinoId > 0 && $inquilinoId === $propietarioId) {
            $errors[] = 'El inquilino y el propietario no pueden ser la misma persona.';
        }
        if ($montoInicial <= 0) {
            $errors[] = 'El monto inicial debe ser mayor a cero.';
        }
        if (!$fechaInicio || !DateTime::createFromFormat('Y-m-d', $fechaInicio)) {
            $errors[] = 'La fecha de inicio no es válida.';
        }
        if ($duracionMeses < 1 || $duracionMeses > 60) {
            $errors[] = 'La duración debe ser entre 1 y 60 meses.';
        }
        if (!in_array($frecuenciaAjuste, [1, 3, 4, 6, 12], true)) {
            $errors[] = 'La frecuencia de ajuste no es válida.';
        }

        if ($errors) {
            set_flash('error', implode(' ', $errors));
            redirect('/contratos/nuevo');
        }

        $contratoModel = new Contrato();
        $contratoId = $contratoModel->create([
            'property_id'             => $propertyId,
            'inquilino_id'            => $inquilinoId,
            'propietario_id'          => $propietarioId,
            'monto_inicial'           => $montoInicial,
            'fecha_inicio'            => $fechaInicio,
            'duracion_meses'          => $duracionMeses,
            'frecuencia_ajuste_meses' => $frecuenciaAjuste,
        ]);

        $cuotaModel = new Cuota();
        $cuotaModel->generarParaContrato($contratoId, $fechaInicio, $duracionMeses, $frecuenciaAjuste, $montoInicial);

        // Vinculamos la propiedad con su inquilino actual
        $propertyModel->update($propertyId, array_merge($property, [
            'propietario_id' => $propietarioId,
            'estado'         => 'alquilada',
        ]));
        $this->setInquilino($propertyId, $inquilinoId);

        set_flash('success', 'Contrato creado con sus ' . $duracionMeses . ' cuotas.');
        redirect('/contratos/' . $contratoId);
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $contrato = $this->findOrFail((int) $id);
        $this->authorizeView($contrato);

        $cuotaModel = new Cuota();
        $cuotas = $cuotaModel->listByContrato((int) $contrato['id']);

        $this->renderDashboard('contratos/show', [
            'contrato' => $contrato,
            'cuotas'   => $cuotas,
            'csrf'     => csrf_token(),
        ]);
    }

    /**
     * Admin/agente dispara el intento de cálculo automático por ICL para todas
     * las cuotas "a convenir" de este contrato cuyo mes ya llegó.
     */
    public function actualizarIcl(string $id): void
    {
        Auth::requireRole('administrador', 'agente');
        $contrato = $this->findOrFail((int) $id);
        $this->authorizeManage($contrato);

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token de seguridad inválido, volvé a intentar.');
            redirect('/contratos/' . $contrato['id']);
        }

        $cuotaModel = new Cuota();
        $bcra = new BcraService();

        $pendientes = $cuotaModel->pendientesDeAjuste((int) $contrato['id']);
        $actualizadas = 0;
        $sinDatos = 0;

        foreach ($pendientes as $cuota) {
            $base = $cuotaModel->ultimoMontoDefinido((int) $contrato['id'], (int) $cuota['numero_cuota']);
            $montoBase = $base ? (float) $base['monto'] : (float) $contrato['monto_inicial'];
            $fechaBase = $base ? $base['mes_vencimiento'] : $contrato['fecha_inicio'];

            $resultado = $bcra->calcularAjuste($montoBase, $fechaBase, $cuota['mes_vencimiento']);

            if ($resultado === null) {
                $sinDatos++;
                continue;
            }

            $cuotaModel->aplicarAjusteIcl(
                (int) $cuota['id'],
                $resultado['monto'],
                $resultado['icl_base'],
                $resultado['icl_actual']
            );
            $actualizadas++;
        }

        if ($actualizadas > 0) {
            set_flash('success', "Se actualizaron $actualizadas cuota(s) con el ajuste ICL del BCRA.");
        }
        if ($sinDatos > 0) {
            set_flash('error', "$sinDatos cuota(s) no se pudieron calcular (sin conexión a la API del BCRA o sin dato disponible para esa fecha). Quedaron como 'a convenir' para cargar el monto a mano.");
        }
        if ($actualizadas === 0 && $sinDatos === 0) {
            set_flash('success', 'No había cuotas pendientes de ajuste por ahora.');
        }

        redirect('/contratos/' . $contrato['id']);
    }

    private function setInquilino(int $propertyId, int $inquilinoId): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE properties SET inquilino_id = :inquilino_id WHERE id = :id');
        $stmt->execute(['inquilino_id' => $inquilinoId, 'id' => $propertyId]);
    }

    private function findOrFail(int $id): array
    {
        $contratoModel = new Contrato();
        $contrato = $contratoModel->find($id);
        if (!$contrato) {
            http_response_code(404);
            require VIEWS_DIR . '/errors/404.php';
            exit;
        }
        return $contrato;
    }

    private function authorizeView(array $contrato): void
    {
        if (Auth::hasRole('cliente')
            && (int) $contrato['inquilino_id'] !== Auth::id()
            && (int) $contrato['propietario_id'] !== Auth::id()
        ) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
        if (Auth::hasRole('agente') && (int) $contrato['agente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }

    private function authorizeManage(array $contrato): void
    {
        if (Auth::hasRole('agente') && (int) $contrato['agente_id'] !== Auth::id()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }
}
