<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $propertyModel = new Property();

        // Destacadas: últimas 3 disponibles, sin importar el rol (página pública)
        $destacadas = $propertyModel->paginated(3, 0, ['estado' => null]);
        // (si querés filtrar solo 'disponible', se puede agregar ese filtro al modelo)

        $this->render('home/index', [
            'destacadas' => $destacadas,
            'csrf' => csrf_token(),
            'transparentHeader' => true,
        ]);
    }
}
