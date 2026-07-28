<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $propertyModel = new Property();

        // Destacadas: últimas 3 disponibles, sin importar el rol (página pública)
        $destacadas = $propertyModel->paginated(3, 0, ['estado' => null]);
        // (si querés filtrar solo 'disponible', se puede agregar ese filtro al modelo)

        $stats = [
            'totalPropiedades' => $propertyModel->count(),
            'zonas'            => $propertyModel->countZonasDistintas(),
            'porTipo'          => $propertyModel->countByTipo(),
        ];

        $this->render('home/index', [
            'destacadas' => $destacadas,
            'stats'      => $stats,
            'csrf' => csrf_token(),
            'transparentHeader' => true,
        ]);
    }
}
