<?php

namespace App\Controllers\Web;

use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;
use App\Repositories\ValidatorRepository;

class DashboardController extends AbstractController
{
    public function index(): Response
    {
        // Obtener solo los incidentes pendientes de validación
        $validatorRepository = new ValidatorRepository();
        $pendingIncidents = $validatorRepository->getPendingIncidents();

        // TODO: Incidentes en revision, mostrar
        
        // Mapear los tipos de incidentes
        $incidentTypes = [
            1 => 'Accidente de Tráfico',
            2 => 'Inundación', 
            3 => 'Incendio',
            4 => 'Robo'
        ];

        // Mapear los roles
        $roles = [
            1 => 'Reportero',
            2 => 'Validador',
            3 => 'Admin',
        ];

        return $this->renderWithFlash('dashboard/index.html.twig', [
            'pendingIncidents' => $pendingIncidents,
            'incidentTypes' => $incidentTypes,
            'roles' => $roles
        ]);
    }
}
