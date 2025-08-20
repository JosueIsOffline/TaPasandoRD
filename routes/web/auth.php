<?php

use App\Controllers\Web\AuthController;

return [
  ['GET', '/login', [AuthController::class, 'showLogin']],
  ['GET', '/register', [AuthController::class, 'showRegister']],
];
