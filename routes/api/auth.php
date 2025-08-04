<?php

use App\Controllers\Api\AuthController;

return [
  ['POST', '/api/register', [AuthController::class, 'register']],
  ['POST', '/api/login', [AuthController::class, 'login']],
  ['POST', '/api/logout', [AuthController::class, 'logout']],

  // Providers routes
  ['GET', '/auth/{provider:[a-z]+}', [AuthController::class, 'proRedirect']],
  ['GET', '/auth/{provider:[a-z]+}/callback', [AuthController::class, 'callback']]
];
