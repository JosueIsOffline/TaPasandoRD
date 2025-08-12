<?php

use App\Controllers\Web\IncidentController;

return [
  ['GET', '/report-incident', [IncidentController::class, 'index'], 'auth']
];
