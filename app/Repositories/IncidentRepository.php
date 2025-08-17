<?php

namespace App\Repositories;

use App\Models\Incident;
use App\Models\User;
use JosueIsOffline\Framework\Database\DB;

class IncidentRepository
{
  public function getAll(): ?array
  {
    $incident = new Incident();
    $incidents = $incident->query()->get();
    return $incidents;
  }

  public function getValidIncident(): ?array
  {
    return Incident::query()->where('status', 'validado')->get();
  }

  public function getPendingIncidents(): ?array
  {
    $sql = "
      SELECT i.*, 
            p.name as province_name,
            m.name as municipality_name,
            n.name as neighborhood_name,
            c.name as category_name,
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
      ORDER BY i.occurrence_date DESC
    ";

    $stmt = DB::raw($sql);
    return $results = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: null;
  }

  public function getById(int $id): ?array
  {
    $sql = "
      SELECT i.*, 
            i.description,
            p.name as province_name,
            m.name as municipality_name,
            n.name as neighborhood_name,
            c.name as category_name,
            reporter.name as reporter_name,
            reporter.email as reporter_email,
            reporter.role_id as reporter_role
      FROM incidents i
      LEFT JOIN provinces p ON i.province_id = p.id
      LEFT JOIN municipalities m ON i.municipality_id = m.id
      LEFT JOIN neighborhoods n ON i.neighborhood_id = n.id
      LEFT JOIN categories c ON i.category_id = c.id
      LEFT JOIN users reporter ON i.reported_by = reporter.id
      WHERE i.id = ?
    ";

    $stmt = DB::raw($sql, [$id]);
    $incident = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if ($incident) {
      $incident['validation_comments'] = $this->getValidationComments($id);
    }
    
    return $incident;
  }

  /**
   * Obtiene los comentarios de validación para un incidente específico
   * TODO: Implementar cuando esté la tabla de comentarios de validación
   */
  private function getValidationComments(int $incidentId): array
  {
    // TODO: Implementar cuando esté la tabla de comentarios de validación
    // Por ahora retornamos un array vacío
    return [];
  }

  public function create(array $data): void
  {
    Incident::query()->insert($data);
  }

  public function update(array $data): void
  {
    Incident::query()->update($data);
  }
}
