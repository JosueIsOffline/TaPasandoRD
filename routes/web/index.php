<?php

use App\Controllers\Web\HomeController;

return [
  ['GET', '/', [HomeController::class, 'index'], 'auth'],
];
