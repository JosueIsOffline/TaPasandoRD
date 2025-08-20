<?php

use App\Controllers\Web\AdministrationController;

return [
  ['GET', '/administration-panel', [AdministrationController::class, 'index'],  ['auth', 'role:admin']]
];
