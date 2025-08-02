<?php

use App\Controllers\Api\AuthController;

return [
  ['POST', '/register', [AuthController::class, 'register']],
  ['POST', '/login', [AuthController::class, 'login']],

  // Providers routes
  ['GET', '/auth/google', [AuthController::class, 'proRedirect']],
  ['GET', '/auth/google/callback', [AuthController::class, 'callback']]
];
