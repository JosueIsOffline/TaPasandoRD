<?php

use App\Repositories\IncidentRepository;
use Tests\TestCase;

class IncidentRepositoryTest extends TestCase
{
  private IncidentRepository $incidentRepo;

  protected function setUp(): void
  {
    parent::setUp();
    $this->incidentRepo = new IncidentRepository();
  }

  public function testGetAllIncidents(): void
  {
    // First, create two test incidents
    $data1 = [
      'occurrence_date' => '2025-08-08',
      'title' => 'Incident One',
      'description' => 'First test incident',
      'latitude' => 18.4861,
      'longitude' => -69.9312,
      'deaths' => 0,
      'injuries' => 0,
      'estimated_loss' => 100.00,
      'social_media_url' => null,
      'photo_url' => null,
      'status' => 'pendiente',
      'validation_date' => null,
      'province_id' => 1,
      'municipality_id' => 1,
      'neighborhood_id' => 1,
      'category_id' => 1,
      'reported_by' => 1,
      'validated_by' => null
    ];

    $data2 = [
      'occurrence_date' => '2025-08-09',
      'title' => 'Incident Two',
      'description' => 'Second test incident',
      'latitude' => 19.0000,
      'longitude' => -70.0000,
      'deaths' => 1,
      'injuries' => 2,
      'estimated_loss' => 500.00,
      'social_media_url' => 'https://twitter.com/test',
      'photo_url' => 'https://example.com/test.jpg',
      'status' => 'validado',
      'validation_date' => '2025-08-10',
      'province_id' => 2,
      'municipality_id' => 2,
      'neighborhood_id' => 2,
      'category_id' => 2,
      'reported_by' => 2,
      'validated_by' => 3
    ];

    $this->incidentRepo->create($data1);
    $id1 = $this->getLastInsertId();
    $this->incidentRepo->create($data2);
    $id2 = $this->getLastInsertId();

    // Retrieve all incidents
    $incidents = $this->incidentRepo->getAll();

    // Assertions
    $this->assertIsArray($incidents, 'Expected result to be an array');
    $this->assertGreaterThanOrEqual(2, count($incidents), 'Expected at least 2 incidents in the list');

    // Check that each element has all the expected keys
    $expectedKeys = array_keys($data1);
    $expectedKeys[] = 'id'; // Assuming your repository adds an ID

    foreach ($incidents as $incident) {
      $this->assertIsArray($incident, 'Each incident should be an array');
      foreach ($expectedKeys as $key) {
        $this->assertArrayHasKey($key, $incident, "Incident is missing key: $key");
      }
    }

    // Optional: Verify that the incidents we created are in the returned list
    $titles = array_column($incidents, 'title');
    $this->assertContains('Incident One', $titles);
    $this->assertContains('Incident Two', $titles);
  }

  public function testCreateIncident(): void
  {
    $data = [
      'occurrence_date' => '2025-08-08',
      'title' => 'Test Incident',
      'description' => 'Detailed description of the test incident',
      'latitude' => 18.4861,
      'longitude' => -69.9312,
      'deaths' => 0,
      'injuries' => 1,
      'estimated_loss' => 1000.50,
      'social_media_url' => 'https://twitter.com/example',
      'photo_url' => 'https://example.com/image.jpg',
      'status' => 'pendiente',
      'validation_date' => null,
      'province_id' => 1,
      'municipality_id' => 1,
      'neighborhood_id' => 1,
      'category_id' => 1,
      'reported_by' => 1,
      'validated_by' => null
    ];

    $this->incidentRepo->create($data);

    $id = (int)$this->getLastInsertId();



    $this->assertIsInt($id);
    $this->assertGreaterThan(0, $id);

    $incident = $this->incidentRepo->getById(1);

    print $id;
    $this->assertArrayHasKey('title', $incident);

    $this->assertEquals($data['title'], $incident['title']);


    $this->assertEquals($data['description'], $incident['description']);

    $this->assertEquals($data['status'], $incident['status']);
  }

  // public function testGetByIdReturnsNullWhenNotFound(): void
  // {
  //   $incident = $this->incidentRepo->getById(999999);
  //   $this->assertNull($incident);
  // }
  //
  // public function testUpdateIncident(): void
  // {
  //   $id = $this->incidentRepo->create([
  //     'occurrence_date' => '2025-08-08',
  //     'title' => 'Old Title',
  //     'description' => 'Old Description',
  //     'latitude' => 18.4861,
  //     'longitude' => -69.9312,
  //     'deaths' => 0,
  //     'injuries' => 0,
  //     'estimated_loss' => 0.00,
  //     'social_media_url' => null,
  //     'photo_url' => null,
  //     'status' => 'open',
  //     'validation_date' => null,
  //     'province_id' => 1,
  //     'municipality_id' => 1,
  //     'neighborhood_id' => 1,
  //     'category_id' => 1,
  //     'reported_by' => 1,
  //     'validate_by' => null
  //   ]);
  //
  //   $updatedData = [
  //     'title' => 'Updated Title',
  //     'description' => 'Updated Description',
  //     'status' => 'closed'
  //   ];
  //
  //   $result = $this->incidentRepo->update($id, $updatedData);
  //   $this->assertTrue($result);
  //
  //   $incident = $this->incidentRepo->getById($id);
  //   $this->assertEquals($updatedData['title'], $incident['title']);
  //   $this->assertEquals($updatedData['description'], $incident['description']);
  //   $this->assertEquals($updatedData['status'], $incident['status']);
  // }
  //
  // public function testDeleteIncident(): void
  // {
  //   $id = $this->incidentRepo->create([
  //     'occurrence_date' => '2025-08-08',
  //     'title' => 'To Delete',
  //     'description' => 'This record will be deleted',
  //     'latitude' => 18.4861,
  //     'longitude' => -69.9312,
  //     'deaths' => 0,
  //     'injuries' => 0,
  //     'estimated_loss' => 0.00,
  //     'social_media_url' => null,
  //     'photo_url' => null,
  //     'status' => 'open',
  //     'validation_date' => null,
  //     'province_id' => 1,
  //     'municipality_id' => 1,
  //     'neighborhood_id' => 1,
  //     'category_id' => 1,
  //     'reported_by' => 1,
  //     'validate_by' => null
  //   ]);
  //
  //   $result = $this->incidentRepo->delete($id);
  //   $this->assertTrue($result);
  //
  //   $incident = $this->incidentRepo->getById($id);
  //   $this->assertNull($incident);
  // }
}
