<?php

use App\Controllers\Web\MapController;

return [
  ['GET', '/map', [MapController::class, 'index'], 'auth'],
];
