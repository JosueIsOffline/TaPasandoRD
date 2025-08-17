<?php

namespace App\Controllers\Api;

use App\Repositories\ValidatorRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class ValidatorController extends AbstractController
{

  private ValidatorRepository $vRepo;

  public function __construct()
  {
    $this->vRepo = new ValidatorRepository();
  }

  public function getPendingIncident(): Response
  {
    $data = $this->vRepo->getPendingIncidents();

    return $this->success($data, 200);
  }
  public function approve(): Response
  {
    $params = $this->request->getAllPost();

    $data = $this->normalizeData($params);
    $data['status'] = "Aprovado";
    $this->vRepo->approveIncident($data);

    return $this->success([], "Reporte Aprobado", 201, '/');
  }

  public function reject(): Response
  {
    $params = $this->request->getAllPost();
    $data = $this->normalizeData($params);
    $data['status'] = "Rechazado";
    $this->vRepo->rejectIncident($data);

    return $this->success([], 'Reporte Rechazado', 201, '/');
  }

  private function normalizeData(array $data): array
  {
    $data['validator_id'] = (int)$data['validator_id'];
    $data['incident_id'] = (int)$data['incident_id'];

    return $data;
  }
}
