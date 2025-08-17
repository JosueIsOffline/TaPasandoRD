<?php

namespace App\Repositories;

use App\Models\Incident;

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
