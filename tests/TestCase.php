<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use JosueIsOffline\Framework\Database\Connection as DatabaseConnection;
use JosueIsOffline\Framework\Database\DB;
use PDO;

abstract class TestCase extends BaseTestCase
{
  protected PDO $pdo;
  protected ?DatabaseConnection $connection = null;

  protected function setUp(): void
  {
    $this->pdo = new PDO('sqlite::memory:');
    DB::configure([
      'driver' => 'sqlite',
      'database' => 'sqlite::memory'
    ]);

    $this->connection = DB::connection();

    // Crear todas las tablas necesarias
    $this->createTables();
  }

  protected function tearDown(): void
  {
    $this->connection = null;
    parent::tearDown();
  }
  protected function getLastInsertId(): int
  {
    $result = $this->connection->query("SELECT last_insert_rowid() as last_id");
    return (int) ($result[0]['last_id'] ?? 0);
  }


  /**
   * Crear todas las tablas del esquema
   */
  private function createTables(): void
  {
    $this->connection->execute('CREATE TABLE IF NOT EXISTS roles (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(50) NOT NULL
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100),
      email VARCHAR(100) UNIQUE,
      password VARCHAR(255) NULL,
      photo_url VARCHAR(255),
      supplier_auth VARCHAR(20) NOT NULL CHECK(supplier_auth IN ("google", "microsoft", "local")),
      role_id INTEGER,
      active BOOLEAN DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (role_id) REFERENCES roles(id)
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS provinces (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      code VARCHAR(10),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS municipalities (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      code VARCHAR(10),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      province_id INTEGER,
      FOREIGN KEY (province_id) REFERENCES provinces(id)
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS neighborhoods (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      municipality_id INTEGER,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (municipality_id) REFERENCES municipalities(id)
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS categories (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name VARCHAR(100) NOT NULL,
      icon_color VARCHAR(10),
      icon VARCHAR(10),
      active BOOLEAN DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS incidents (
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

    $this->connection->execute('CREATE TABLE IF NOT EXISTS incidentValidations (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      incident_id INTEGER,
      validator_id INTEGER,
      status VARCHAR(20) CHECK(status IN ("Aprovado", "Rechazado")),
      comments TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (incident_id) REFERENCES incidents(id),
      FOREIGN KEY (validator_id) REFERENCES users(id)
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS incidentCategories (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      incident_id INTEGER,
      category_id INTEGER,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (incident_id) REFERENCES incidents(id),
      FOREIGN KEY (category_id) REFERENCES categories(id)
    )');

    $this->connection->execute('CREATE TABLE IF NOT EXISTS comments (
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
}
