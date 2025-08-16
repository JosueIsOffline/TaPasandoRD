<?php

use App\Controllers\Api\ValidatorController;

return [
  ['GET', '/api/validator/get-pending', [ValidatorController::class, 'getPendingIncident']]
];
