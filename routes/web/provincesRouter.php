<?php

use App\Controllers\Web\ProvinceController;

return [
    ['GET', '/provinces', [ProvinceController::class, 'index']],
    ['GET', '/provinces/create', [ProvinceController::class, 'createForm']],
    ['POST', '/provinces/store', [ProvinceController::class, 'store']],
    ['GET', '/provinces/edit', [ProvinceController::class, 'editForm']],
    ['POST', '/provinces/update', [ProvinceController::class, 'update']],
    ['POST', '/provinces/delete', [ProvinceController::class, 'destroy']],
];
