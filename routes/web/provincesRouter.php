<?php

use App\Controllers\Web\ProvinceController;

return [
    ['GET', '/provinces', [ProvinceController::class, 'index']],
    ['GET', '/provinces/create', [ProvinceController::class, 'create']],
    ['POST', '/provinces', [ProvinceController::class, 'store']],
    ['GET', '/provinces/{id}/edit', [ProvinceController::class, 'edit']],
    ['POST', '/provinces/{id}/update', [ProvinceController::class, 'update']],
    ['GET', '/provinces/{id}/delete', [ProvinceController::class, 'destroy']],
];

