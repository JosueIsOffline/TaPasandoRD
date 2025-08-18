<?php

use App\Controllers\Web\IncidentController;
use App\Controllers\Web\ProvinceController;

return [
  ['GET', '/province', [ProvinceController::class, 'index'], 'auth'],
  ['GET', '/get-province', [ProvinceController::class, 'list'], 'auth'],

];