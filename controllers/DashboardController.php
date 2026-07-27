<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('administrador', 'agente');

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

        $this->render('dashboard/index', $data);
    }
}
