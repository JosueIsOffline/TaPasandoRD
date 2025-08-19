<?php

use App\Controllers\Web\ValidationReportController;

return [
    ['GET', '/validation-report', [ValidationReportController::class, 'index']],
];
