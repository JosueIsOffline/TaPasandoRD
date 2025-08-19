<?php

namespace App\Repositories;

use App\Models\Incident;
use App\Models\IncidentValidation;
use JosueIsOffline\Framework\Database\DB;

class ValidatorRepository
{
  public function getPendingIncidents(): ?array
  {
    $sql = "
      SELECT i.*, 
            p.name as province_name,
            m.name as municipality_name,
            n.name as neighborhood_name,
            c.name as category_name,
            c.icon_color as category_color,
            reporter.name as reporter_name,
            reporter.email as reporter_email,
            reporter.role_id as reporter_role
      FROM incidents i
      LEFT JOIN provinces p ON i.province_id = p.id
      LEFT JOIN municipalities m ON i.municipality_id = m.id
      LEFT JOIN neighborhoods n ON i.neighborhood_id = n.id
      LEFT JOIN categories c ON i.category_id = c.id
      LEFT JOIN users reporter ON i.reported_by = reporter.id
      WHERE i.status = 'pendiente'
      ORDER BY i.occurrence_date ASC
    ";


    $stmt = DB::raw($sql);
    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: null;
    return $results;
  }

  public function getValidationComments(int $incidentId): ?array
  {
    $sql = "
      SELECT iv.*, u.name as validator_name, u.email as validator_email
      FROM incidentValidations iv
      LEFT JOIN users u ON iv.validator_id = u.id
      WHERE iv.incident_id = ?
      ORDER BY iv.created_at DESC
    ";

    $stmt = DB::raw($sql, [$incidentId]);
    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: null;
    return $results;
  }

  public function approveIncident(array $data): void
  {

    $validate = new IncidentValidation();

    $validate->create($data);
    $this->changeStatusIncident($data['incident_id'], 'validado');
  }

  public function rejectIncident(array $data): void
  {
    $validate = new IncidentValidation();

    $validate->create($data);

    $this->changeStatusIncident($data['incident_id'], 'rechazado');
  }

  private function changeStatusIncident(int $id, string $status): void
  {
    $incident = new Incident();
    $incident->update(['id' => $id, 'status' => $status]);
  }
}
