<?php

namespace App\Controllers\Web;

use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;
use App\Repositories\ValidatorRepository;

class ValidationReportController extends AbstractController
{
    public function index(): Response
    {
        // Obtener solo los incidentes pendientes de validación
        // Incluye: título, fecha, tipo con color, ubicación, reportero y estado
        $validatorRepository = new ValidatorRepository();
        $pendingIncidents = $validatorRepository->getPendingIncidents();
        
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

        return $this->renderWithFlash('validationReport/index.html.twig', [
            'pendingIncidents' => $pendingIncidents,
            'incidentTypes' => $incidentTypes,
            'roles' => $roles
        ]);
    }
}
