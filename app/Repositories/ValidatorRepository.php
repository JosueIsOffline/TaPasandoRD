<?php

namespace App\Repositories;

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
    return $results = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: null;
  }

  public function approveIncident(array $data): void
  {
    IncidentValidation::query()->insert($data);
  }

  public function rejectIncident(array $data): void
  {
    IncidentValidation::query()->insert($data);
  }
}
