<?php

use App\Controllers\Web\IncidentController;

return [
  ['GET', '/incident', [IncidentController::class, 'index'], 'auth']
];
