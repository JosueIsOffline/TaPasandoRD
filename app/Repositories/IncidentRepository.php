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

  public function getIncidentById(int $id): Response
  {
    $incident = $this->iRepo->getById($id);

    if (!$incident) {
      return $this->error('Incidente no encontrado', 404);
    }

    return $this->success($incident);
  }

 public function getFilteredIncident(array $params): ?array
  {
      $incident = new Incident();
      $filtered = [];

      foreach ($params as $key => $value) {
          $results = $incident->query()->where($key, $value)->get();
          foreach ($results as $item) {
              $filtered[$item['id']] = $item; // Usa el id como clave para evitar duplicados
          }
      }

      return array_values($filtered); // Devuelve solo los valores (sin las claves)
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
    return Incident::query()->where('id', $id)->first();
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
