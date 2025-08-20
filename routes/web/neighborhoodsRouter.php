<?php

use App\Controllers\Web\NeighborhoodController;

return [
    ['GET', '/neighborhoods', [NeighborhoodController::class, 'index']],
    ['GET', '/neighborhoods/create', [NeighborhoodController::class, 'create']],
    ['POST', '/neighborhoods', [NeighborhoodController::class, 'store']],
    ['GET', '/neighborhoods/{id}/edit', [NeighborhoodController::class, 'edit']],
    ['POST', '/neighborhoods/{id}/update', [NeighborhoodController::class, 'update']],
    ['GET', '/neighborhoods/{id}/delete', [NeighborhoodController::class, 'destroy']],
];
?>