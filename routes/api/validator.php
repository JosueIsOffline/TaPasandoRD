<?php

use App\Controllers\Api\ValidatorController;

return [
  ['GET', '/api/validator/get-pending', [ValidatorController::class, 'getPendingIncident']],
  ['POST', '/api/validator/approve', [ValidatorController::class, 'approve']],
  ['POST', '/api/validator/reject', [ValidatorController::class, 'reject']]
];
