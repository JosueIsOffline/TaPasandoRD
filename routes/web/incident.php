<?php

use App\Controllers\Web\IncidentController;

return [
  ['GET', '/report-incident', [IncidentController::class, 'index'], 'auth'],
  ['GET', '/incidents', [IncidentController::class, 'list'], 'auth']
];
