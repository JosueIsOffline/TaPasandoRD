<?php

use App\Controllers\Api\IncidentController;

return [
  ['GET', '/api/incident', [IncidentController::class, 'getIncidents'], 'auth']
];
