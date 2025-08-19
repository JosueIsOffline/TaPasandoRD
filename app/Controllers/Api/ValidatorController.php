<?php

namespace App\Controllers\Api;

use App\Repositories\ValidatorRepository;
use JosueIsOffline\Framework\Auth\AuthService;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class ValidatorController extends AbstractController
{

  private AuthService $auth;
  private ValidatorRepository $vRepo;

  public function __construct()
  {
    $this->vRepo = new ValidatorRepository();
    $this->auth = new AuthService();
  }

  public function getPendingIncident(): Response
  {
    $data = $this->vRepo->getPendingIncidents();

    return $this->success($data, 200);
  }

  public function getValidationComments(int $id): Response
  {
    $comments = $this->vRepo->getValidationComments($id);
    
    // Si no hay comentarios, devolver un array vacío
    if ($comments === null) {
      $comments = [];
    }
    
    return $this->success($comments, 200);
  }

  public function approve(): Response
  {
    $params = $this->request->getAllPost();

    $data = $this->normalizeData($params);
    $data['validator_id'] = $this->auth->id();
    $data['status'] = "Aprovado";
    $this->vRepo->approveIncident($data);

    return $this->success([], "Reporte Aprobado", 201, '/');
  }

  public function reject(): Response
  {
    $params = $this->request->getAllPost();
    $data = $this->normalizeData($params);
    $data['validator_id'] = $this->auth->id();
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
