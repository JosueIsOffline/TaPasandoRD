<?php

use App\Controllers\Web\MunicipalityController;

return [
    ['GET', '/municipalities', [MunicipalityController::class, 'index']],
    ['GET', '/municipalities/create', [MunicipalityController::class, 'create']],
    ['POST', '/municipalities', [MunicipalityController::class, 'store']],
    ['GET', '/municipalities/{id}/edit', [MunicipalityController::class, 'edit']],
    ['POST', '/municipalities/{id}/update', [MunicipalityController::class, 'update']],
    ['GET', '/municipalities/{id}/delete', [MunicipalityController::class, 'destroy']],
];
