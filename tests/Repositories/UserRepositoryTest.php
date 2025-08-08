<?php

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\AtomicTestCase;

/**
 * Test completamente aislado - cada método ejecuta en su propia base de datos
 */
class UserRepositoryTest extends AtomicTestCase
{
  private UserRepository $repo;

  public function setUp(): void
  {
    parent::setUp();
    $this->repo = new UserRepository();

    // Verificar que empezamos con tablas limpias (excepto datos semilla)
    $this->assertTableCount('roles', 3); // reporter, validator, admin
    $this->assertTableEmpty('users');
  }

  public function testCreateUser(): void
  {
    $uniqueEmail = 'josue_' . uniqid() . '@tapasandord.com';

    $userData = [
      'name' => "Josué Hernández",
      'email' => $uniqueEmail,
      'password' => password_hash("securepassword", PASSWORD_DEFAULT),
      'photo_url' => "https://example.com/photo.jpg",
      'supplier_auth' => "local",
      'role_id' => 1,
    ];

    // La tabla debe estar vacía al inicio
    $this->assertTableEmpty('users');

    $this->repo->create($userData);

    // Ahora debe tener 1 usuario
    $this->assertTableCount('users', 1);

    $user = $this->repo->getById(1);

    $this->assertNotNull($user);
    $this->assertEquals('Josué Hernández', $user['name']);
    $this->assertEquals($uniqueEmail, $user['email']);
    $this->assertEquals('local', $user['supplier_auth']);
    $this->assertEquals(1, $user['role_id']);
  }

  public function testFindByEmail(): void
  {
    $this->assertTableEmpty('users');

    $testUser = $this->createUniqueUser([
      'name' => 'María González'
    ]);

    $foundUser = $this->repo->findByEmail($testUser['email']);

    $this->assertNotNull($foundUser);
    $this->assertEquals('María González', $foundUser['name']);
    $this->assertEquals($testUser['email'], $foundUser['email']);

    $this->assertTableCount('users', 1);
  }

  public function testFindByEmailNotFound(): void
  {
    $this->assertTableEmpty('users');

    $user = $this->repo->findByEmail('noexiste@example.com');

    $this->assertNull($user);
    $this->assertTableEmpty('users');
  }

  public function testUpdateUser(): void
  {
    $this->assertTableEmpty('users');

    $testUser = $this->createUniqueUser([
      'name' => 'Pedro Martínez',
      'supplier_auth' => 'local'
    ]);

    $this->assertTableCount('users', 1);

    $updateData = [
      'id' => $testUser['id'],
      'supplier_auth' => 'google',
      'photo_url' => 'https://newphoto.com/updated.jpg'
    ];

    $this->repo->update($updateData);

    $updatedUser = $this->repo->getById($testUser['id']);

    $this->assertEquals('google', $updatedUser['supplier_auth']);
    $this->assertEquals('https://newphoto.com/updated.jpg', $updatedUser['photo_url']);
    $this->assertEquals('Pedro Martínez', $updatedUser['name']);

    // Sigue siendo 1 usuario
    $this->assertTableCount('users', 1);
  }

  public function testFindByEmailAndProviderWithUniqueEmails(): void
  {
    $this->assertTableEmpty('users');

    $baseEmail = 'test_' . uniqid() . '@example.com';

    // Crear usuario local
    $localUser = $this->createUniqueUser([
      'email' => $baseEmail,
      'supplier_auth' => 'local',
      'name' => 'Local User'
    ]);

    // Crear usuario Google con mismo email
    $googleUser = $this->createUniqueUser([
      'email' => $baseEmail,
      'supplier_auth' => 'google',
      'name' => 'Google User'
    ]);

    $this->assertTableCount('users', 2);

    // Buscar usuario local
    $foundLocal = $this->repo->findByEmailAndProvider($baseEmail, 'local');
    $this->assertNotNull($foundLocal);
    $this->assertEquals('local', $foundLocal['supplier_auth']);
    $this->assertEquals('Local User', $foundLocal['name']);

    // Buscar usuario de Google
    $foundGoogle = $this->repo->findByEmailAndProvider($baseEmail, 'google');
    $this->assertNotNull($foundGoogle);
    $this->assertEquals('google', $foundGoogle['supplier_auth']);
    $this->assertEquals('Google User', $foundGoogle['name']);

    // Buscar proveedor que no existe
    $notFound = $this->repo->findByEmailAndProvider($baseEmail, 'microsoft');
    $this->assertNull($notFound);
  }

  public function testGetByIdNotFound(): void
  {
    $this->assertTableEmpty('users');

    $user = $this->repo->getById(999);

    $this->assertNull($user);
    $this->assertTableEmpty('users');
  }

  public function testCreateMultipleUsersWithUniqueEmails(): void
  {
    $this->assertTableEmpty('users');

    $createdUsers = [];

    // Crear 5 usuarios únicos
    for ($i = 1; $i <= 5; $i++) {
      $uniqueEmail = 'usuario_' . uniqid() . "_{$i}@test.com";

      $userData = [
        'name' => "Usuario {$i}",
        'email' => $uniqueEmail,
        'password' => password_hash("password{$i}", PASSWORD_DEFAULT),
        'photo_url' => "https://example.com/photo{$i}.jpg",
        'supplier_auth' => 'local',
        'role_id' => 1,
      ];

      $this->repo->create($userData);
      $createdUsers[] = $this->repo->findByEmail($userData['email']);

      // Verificar conteo incremental
      $this->assertTableCount('users', $i);
    }

    // Verificar que todos se crearon correctamente
    $this->assertCount(5, $createdUsers);

    foreach ($createdUsers as $index => $user) {
      $expectedIndex = $index + 1;
      $this->assertEquals("Usuario {$expectedIndex}", $user['name']);
      $this->assertNotNull($user['email']);
      $this->assertTrue(str_contains($user['email'], "usuario_"));
      $this->assertTrue(str_contains($user['email'], "_{$expectedIndex}@test.com"));
    }

    $this->assertTableCount('users', 5);
  }

  public function testCreateUserWithOAuthProvider(): void
  {
    $this->assertTableEmpty('users');

    $uniqueEmail = 'googleuser_' . uniqid() . '@gmail.com';

    $userData = [
      'name' => "Google User",
      'email' => $uniqueEmail,
      'password' => null, // OAuth users don't have passwords
      'photo_url' => "https://lh3.googleusercontent.com/photo.jpg",
      'supplier_auth' => "google",
      'role_id' => 1,
    ];

    $this->repo->create($userData);

    $this->assertTableCount('users', 1);

    $user = $this->repo->findByEmail($uniqueEmail);

    $this->assertNotNull($user);
    $this->assertEquals('Google User', $user['name']);
    $this->assertEquals('google', $user['supplier_auth']);
    $this->assertNull($user['password']);
  }

  public function testCreateDifferentProvidersWithSameEmail(): void
  {
    $this->assertTableEmpty('users');

    $baseEmail = 'shared_' . uniqid() . '@example.com';

    // Crear usuario local
    $localUserData = [
      'name' => "Local User",
      'email' => $baseEmail,
      'password' => password_hash("password123", PASSWORD_DEFAULT),
      'supplier_auth' => "local",
      'role_id' => 1,
    ];
    $this->repo->create($localUserData);
    $this->assertTableCount('users', 1);

    // Crear usuario Google con mismo email
    $googleUserData = [
      'name' => "Google User",
      'email' => $baseEmail,
      'password' => null,
      'supplier_auth' => "google",
      'role_id' => 1,
    ];
    $this->repo->create($googleUserData);
    $this->assertTableCount('users', 2);

    // Crear usuario Microsoft con mismo email
    $microsoftUserData = [
      'name' => "Microsoft User",
      'email' => $baseEmail,
      'password' => null,
      'supplier_auth' => "microsoft",
      'role_id' => 2, // validador
    ];
    $this->repo->create($microsoftUserData);
    $this->assertTableCount('users', 3);

    // Verificar que todos existen con sus respectivos proveedores
    $localUser = $this->repo->findByEmailAndProvider($baseEmail, 'local');
    $googleUser = $this->repo->findByEmailAndProvider($baseEmail, 'google');
    $microsoftUser = $this->repo->findByEmailAndProvider($baseEmail, 'microsoft');

    $this->assertNotNull($localUser);
    $this->assertNotNull($googleUser);
    $this->assertNotNull($microsoftUser);

    $this->assertEquals('Local User', $localUser['name']);
    $this->assertEquals('Google User', $googleUser['name']);
    $this->assertEquals('Microsoft User', $microsoftUser['name']);

    // Verificar que todos tienen IDs diferentes
    $this->assertNotEquals($localUser['id'], $googleUser['id']);
    $this->assertNotEquals($localUser['id'], $microsoftUser['id']);
    $this->assertNotEquals($googleUser['id'], $microsoftUser['id']);
  }

  public function testDatabaseIsolationBetweenTests(): void
  {
    // Este test verifica que cada método comienza con una DB limpia
    $this->assertTableEmpty('users');

    // Si los tests anteriores no hubieran limpiado, habría usuarios aquí
    $allUsers = $this->pdo->query("SELECT * FROM users")->fetchAll();
    $this->assertEmpty($allUsers);

    // Crear un usuario
    $user = $this->createUniqueUser();
    $this->assertTableCount('users', 1);

    // El siguiente test debería empezar con 0 usuarios de nuevo
  }
}
