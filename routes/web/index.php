<?php

use App\Controllers\Web\MapController;

return [
  ['GET', '/', [MapController::class, 'index']]
];
