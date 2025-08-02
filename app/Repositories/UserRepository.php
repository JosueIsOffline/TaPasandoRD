<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

  public function getById(int $id): array
  {
    return [];
  }
  public function create(array $data): void
  {
    $user = new User();
    $user->create($data);
  }
}
