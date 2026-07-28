<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        if (Auth::hasRole('cliente')) {
            $this->indexCliente();
            return;
        }

        $propertyModel = new Property();
        $reclamoModel = new Reclamo();
        $filters = Auth::hasRole('agente') ? ['agente_id' => Auth::id()] : [];

        $data = [
            'totalPropiedades' => $propertyModel->count($filters),
            'porEstado'        => $propertyModel->countByEstado($filters),
            'porTipo'          => $propertyModel->countByTipo($filters),
            'recientes'        => $propertyModel->paginated(5, 0, $filters),
            'reclamosAbiertos' => $reclamoModel->countAbiertos($filters),
        ];

        if (Auth::hasRole('administrador')) {
            $userModel = new User();
            $messageModel = new Message();

            $data['porRol'] = $userModel->countByRole();
            $data['totalUsuarios'] = array_sum($data['porRol']);
            $data['mensajesSinLeer'] = $messageModel->countUnread();
            $data['mensajesRecientes'] = $messageModel->paginated(5, 0);
        }

        $this->renderDashboard('dashboard/index', $data);
    }

    /** Panel simplificado para el cliente: sus propiedades, reclamos y próxima cuota */
    private function indexCliente(): void
    {
        $propertyModel = new Property();
        $reclamoModel = new Reclamo();
        $contratoModel = new Contrato();

        $misPropiedades = $propertyModel->misPropiedades(Auth::id());
        $misReclamos = $reclamoModel->paginated(5, 0, ['cliente_id' => Auth::id()]);
        $misContratos = array_merge(
            $contratoModel->list(['inquilino_id' => Auth::id()]),
            $contratoModel->list(['propietario_id' => Auth::id()])
        );

        $this->renderDashboard('dashboard/cliente', [
            'propiedades' => $misPropiedades,
            'reclamos'    => $misReclamos,
            'contratos'   => $misContratos,
        ]);
    }
}
