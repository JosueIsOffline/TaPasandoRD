<?php

use App\Controllers\Api\AuthController;

return [
  ['POST', '/register', [AuthController::class, 'register']],
  ['POST', '/login', [AuthController::class, 'login']],
];
