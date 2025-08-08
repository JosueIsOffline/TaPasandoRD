<?php

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
  private UserRepository $repo;
  public function setUp(): void
  {
    parent::setUp();
    $this->repo = new UserRepository();

    $this->connection->execute("INSERT INTO roles (name) values ('admin')");
  }

  public function testCreateUser()
  {
    $data = [
      'name' => "Josue",
      'email' => "tese2@gmail.com",
      'password' => "password",
      'photo_url' => "test",
      'supplier_auth' => "local",
      "role_id" => 1,
    ];

    $this->repo->create($data);

    $user = $this->repo->getById(1);

    $this->assertArrayHasKey('name', $user);
  }
}
