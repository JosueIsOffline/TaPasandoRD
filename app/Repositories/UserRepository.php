<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

  public function getById(int $id): ?array
  {
    $user = new User();

    return $user->query()->where('id', $id)->first() ?? null;
  }

  public function findByEmail(string $email): ?array
  {
    $user = new User();

    return $user->query()->where('email', $email)->first();
  }

  public function update(array $data): void
  {
    $user = new User();

    $user->update($data);
  }
  public function create(array $data): void
  {
    $user = new User();
    $user->create($data);
  }

  public function findByEmailAndProvider(string $email, string $provider): ?array
  {
    $user = new User();

    $result = $user->query()->where('email', $email)->where('supplier_auth', $provider)->first();

    return $result ? $result : null;
  }
}
