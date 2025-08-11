<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use JosueIsOffline\Framework\Database\Connection as DatabaseConnection;
use JosueIsOffline\Framework\Database\DB;
use PDO;

/**
 * TestCase completamente aislado - cada test ejecuta en su propia instancia
 */
abstract class TestCase extends BaseTestCase
{
  protected PDO $pdo;
  protected ?DatabaseConnection $connection = null;
  private string $tempDbFile;

  protected function setUp(): void
  {
    // Crear archivo temporal único para cada test
    $this->tempDbFile = tempnam(sys_get_temp_dir(), 'test_db_') . '.sqlite';

    // Crear nueva conexión PDO con archivo temporal
    $this->pdo = new PDO('sqlite:' . $this->tempDbFile);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configurar framework DB
    DB::configure([
      'driver' => 'sqlite',
      'database' => $this->tempDbFile
    ]);

    // Obtener nueva conexión del framework
    $this->connection = DB::connection();

    // Crear esquema completo
    $this->createFullSchema();

    // Insertar datos iniciales
    $this->seedInitialData();
  }

  protected function tearDown(): void
  {
    // Cerrar todas las conexiones
    if ($this->connection) {
      try {
      } catch (\Exception $e) {
        // Ignorar errores al cerrar
      }
    }

    if ($this->pdo) {
    }

    // Eliminar archivo temporal
    if (file_exists($this->tempDbFile)) {
      unlink($this->tempDbFile);
    }

    // Resetear configuración del framework
    try {
    } catch (\Exception $e) {
      // Ignorar errores
    }

    parent::tearDown();
  }

  /**
   * Crear un usuario único para el test actual
   */
  protected function createUniqueUser(array $overrides = []): array
  {
    $unique = uniqid('user_', true);

    $defaultData = [
      'name' => "Test User {$unique}",
      'email' => "test_{$unique}@example.com",
      'password' => password_hash('password123', PASSWORD_DEFAULT),
      'photo_url' => "https://example.com/{$unique}.jpg",
      'supplier_auth' => 'local',
      'role_id' => 1,
      'active' => 1
    ];

    $userData = array_merge($defaultData, $overrides);

    // Insertar directamente en la base de datos
    $sql = "INSERT INTO users (name, email, password, photo_url, supplier_auth, role_id, active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      $userData['name'],
      $userData['email'],
      $userData['password'],
      $userData['photo_url'],
      $userData['supplier_auth'],
      $userData['role_id'],
      $userData['active']
    ]);

    $userData['id'] = $this->pdo->lastInsertId();

    return $userData;
  }

  /**
   * Crear incidente único para el test actual
   */
  protected function createUniqueIncident(array $overrides = []): array
  {
    $unique = uniqid('incident_', true);

    // Crear usuario si no se proporciona
    if (!isset($overrides['reported_by'])) {
      $user = $this->createUniqueUser();
      $overrides['reported_by'] = $user['id'];
    }

    $defaultData = [
      'occurrence_date' => date('Y-m-d H:i:s'),
      'title' => "Test Incident {$unique}",
      'description' => "Description for {$unique}",
      'latitude' => 18.4861 + (rand(1, 1000) * 0.0001),
      'longitude' => -69.9312 + (rand(1, 1000) * 0.0001),
      'deaths' => 0,
      'injuries' => rand(0, 5),
      'estimated_loss' => rand(1000, 50000),
      'status' => 'pendiente',
      'province_id' => 1,
      'municipality_id' => 1,
      'neighborhood_id' => 1,
      'category_id' => 1
    ];

    $incidentData = array_merge($defaultData, $overrides);

    $sql = "INSERT INTO incidents (occurrence_date, title, description, latitude, longitude, deaths, injuries, estimated_loss, status, province_id, municipality_id, neighborhood_id, category_id, reported_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      $incidentData['occurrence_date'],
      $incidentData['title'],
      $incidentData['description'],
      $incidentData['latitude'],
      $incidentData['longitude'],
      $incidentData['deaths'],
      $incidentData['injuries'],
      $incidentData['estimated_loss'],
      $incidentData['status'],
      $incidentData['province_id'],
      $incidentData['municipality_id'],
      $incidentData['neighborhood_id'],
      $incidentData['category_id'],
      $incidentData['reported_by']
    ]);

    $incidentData['id'] = $this->pdo->lastInsertId();

    return $incidentData;
  }

  /**
   * Obtener conteo de registros en una tabla
   */
  protected function getTableCount(string $table): int
  {
    $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM {$table}");
    return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
  }

  /**
   * Verificar que una tabla está vacía
   */
  protected function assertTableEmpty(string $table): void
  {
    $count = $this->getTableCount($table);
    $this->assertEquals(0, $count, "Table {$table} should be empty but has {$count} records");
  }

  /**
   * Verificar que una tabla tiene un número específico de registros
   */
  protected function assertTableCount(string $table, int $expectedCount): void
  {
    $count = $this->getTableCount($table);
    $this->assertEquals($expectedCount, $count, "Table {$table} should have {$expectedCount} records but has {$count}");
  }

  /**
   * Crear esquema completo de la base de datos
   */
  private function createFullSchema(): void
  {
    // Activar foreign keys
    $this->pdo->exec('PRAGMA foreign_keys = ON');

    $this->pdo->exec('CREATE TABLE roles (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(50) NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $this->pdo->exec('CREATE TABLE users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100),
      email VARCHAR(100),
      password VARCHAR(255) NULL,
      photo_url VARCHAR(255),
      supplier_auth VARCHAR(20) NOT NULL CHECK(supplier_auth IN ("google", "microsoft", "local")),
      role_id INTEGER,
      active BOOLEAN DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (role_id) REFERENCES roles(id),
      UNIQUE(email, supplier_auth)
    )');

    $this->pdo->exec('CREATE TABLE provinces (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      code VARCHAR(10),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $this->pdo->exec('CREATE TABLE municipalities (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      code VARCHAR(10),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      province_id INTEGER,
      FOREIGN KEY (province_id) REFERENCES provinces(id)
    )');

    $this->pdo->exec('CREATE TABLE neighborhoods (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      municipality_id INTEGER,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (municipality_id) REFERENCES municipalities(id)
    )');

    $this->pdo->exec('CREATE TABLE categories (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      icon_color VARCHAR(10),
      icon VARCHAR(10),
      active BOOLEAN DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $this->pdo->exec('CREATE TABLE incidents (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      occurrence_date DATETIME NOT NULL,
      title VARCHAR(255),
      description TEXT,
      latitude DECIMAL(10,8),
      longitude DECIMAL(11,8),
      deaths INTEGER DEFAULT 0,
      injuries INTEGER DEFAULT 0,
      estimated_loss DECIMAL(12,2),
      social_media_url VARCHAR(100),
      photo_url VARCHAR(100),
      status VARCHAR(20) CHECK(status IN ("pendiente", "en revisión", "validado", "rechazado")),
      validation_date DATETIME,
      rejection_reason TEXT,
      province_id INTEGER,
      municipality_id INTEGER,
      neighborhood_id INTEGER,
      category_id INTEGER,
      reported_by INTEGER,
      validated_by INTEGER,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (province_id) REFERENCES provinces(id),
      FOREIGN KEY (municipality_id) REFERENCES municipalities(id),
      FOREIGN KEY (neighborhood_id) REFERENCES neighborhoods(id),
      FOREIGN KEY (category_id) REFERENCES categories(id),
      FOREIGN KEY (reported_by) REFERENCES users(id),
      FOREIGN KEY (validated_by) REFERENCES users(id)
    )');

    $this->pdo->exec('CREATE TABLE incidentValidations (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      incident_id INTEGER,
      validator_id INTEGER,
      status VARCHAR(20) CHECK(status IN ("Aprovado", "Rechazado")),
      comments TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (incident_id) REFERENCES incidents(id),
      FOREIGN KEY (validator_id) REFERENCES users(id)
    )');

    $this->pdo->exec('CREATE TABLE incidentCategories (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      incident_id INTEGER,
      category_id INTEGER,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (incident_id) REFERENCES incidents(id),
      FOREIGN KEY (category_id) REFERENCES categories(id)
    )');

    $this->pdo->exec('CREATE TABLE comments (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      incident_id INTEGER,
      user_id INTEGER,
      content TEXT,
      active BOOLEAN DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (incident_id) REFERENCES incidents(id),
      FOREIGN KEY (user_id) REFERENCES users(id)
    )');
  }

  /**
   * Insertar datos iniciales necesarios
   */
  private function seedInitialData(): void
  {
    // Insertar roles
    $this->pdo->exec("INSERT INTO roles (name) VALUES ('reporter')");
    $this->pdo->exec("INSERT INTO roles (name) VALUES ('validator')");
    $this->pdo->exec("INSERT INTO roles (name) VALUES ('admin')");

    // Insertar ubicaciones
    $this->pdo->exec("INSERT INTO provinces (name, code) VALUES ('Santo Domingo', 'SD')");
    $this->pdo->exec("INSERT INTO municipalities (name, code, province_id) VALUES ('Santo Domingo Este', 'SDE', 1)");
    $this->pdo->exec("INSERT INTO neighborhoods (name, municipality_id) VALUES ('Los Mina', 1)");

    // Insertar categorías
    $this->pdo->exec("INSERT INTO categories (name, icon_color, icon, active) VALUES ('Accidente de Tránsito', '#FF0000', 'car', 1)");
    $this->pdo->exec("INSERT INTO categories (name, icon_color, icon, active) VALUES ('Robo', '#FF8800', 'shield', 1)");
  }
}
