<?php

use App\Controllers\Api\IncidentController;

return [
  ['GET', '/api/incident', [IncidentController::class, 'getIncidents']],
  ['GET', '/api/incident/{id}', [IncidentController::class, 'getIncidentById']],
  ['GET', '/api/valid-incident', [IncidentController::class, 'getValidatedIncidents']],
  ['POST', '/api/incident', [IncidentController::class, 'createIncident']]
];
