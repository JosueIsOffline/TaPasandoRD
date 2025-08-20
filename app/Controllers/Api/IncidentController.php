<?php

namespace App\Controllers\Api;

use App\Repositories\IncidentRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class IncidentController extends AbstractController
{
  private IncidentRepository $iRepo;

  public function __construct()
  {
    $this->iRepo = new IncidentRepository();
  }

  public function getIncidents(): Response
  {
    $incidents = $this->iRepo->getAll();

    return $this->success($incidents);
  }

  public function getValidatedIncidents(): Response
  {
    $incidents = $this->iRepo->getValidIncident();
    $queryParams = $this->getQueryParams();

    // condition if params exist then filter incident with those params
    if ($queryParams) {
      // filter method
      $result = $this->iRepo->getFilteredIncident($queryParams);

      return $this->success($result);
    }

    return $this->success($incidents);
  }

  public function getIncidentById(int $id): Response
  {
    $incident = $this->iRepo->getById($id);

    if (!$incident) {
      return $this->error([], 'Incidente no encontrado', 404);
    }

    return $this->success($incident);
  }

  public function createIncident(): Response
  {
    $params = $this->request->getAllPost();

    //TODO: migrate this stuff to normalizeData function
    $date = new \DateTime($params['occurrence_date']);

    $params['occurrence_date'] = $date->format('Y-m-d H:i:s');
    $params['reported_by'] = (int)$params['reported_by'];
    $params['category_id'] = (int)$params['category_id'];
    $params['province_id'] = (int)$params['province_id'];
    $params['municipality_id'] = (int)$params['municipality_id'];
    $params['neighborhood_id'] = (int)$params['neighborhood_id'];
    $params['latitude'] = round((float)$params['latitude'], 8);
    $params['longitude'] = round((float)$params['longitude'], 8);
    $params['deaths'] = $params['deaths'] === '' ? null : (int)$params['deaths'];
    $params['injuries'] = $params['injuries'] === '' ? null : (int)$params['injuries'];
    $params['estimated_loss'] = $params['estimated_loss'] === '' ? null : (float)$params['estimated_loss'];

    $this->iRepo->create($params);

    return $this->success([], 'Reporte de incidencia enviada para su revision. ', 201, '/report-incident');
  }

  public function updateIncident(): Response
  {
    $params = $this->request->getAllPost();

    $this->iRepo->update($params);

    return $this->success([], 'Incidencia actualizada.', 200, null);
  }
  private function normalizeData(array $data): array
  {

    return $data;
  }
}
