<?php

use App\Controllers\Web\CategoryController;

return [
    ['GET', '/categories', [CategoryController::class, 'index']],
    ['GET', '/categories/create', [CategoryController::class, 'create']],
    ['POST', '/categories', [CategoryController::class, 'store']],
    ['GET', '/categories/{id}/edit', [CategoryController::class, 'edit']],
    ['POST', '/categories/{id}/update', [CategoryController::class, 'update']],
    ['GET', '/categories/{id}/delete', [CategoryController::class, 'destroy']],
];
?>