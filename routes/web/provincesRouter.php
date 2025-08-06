<?php

use App\Controllers\ProvinceController;

$router->get('/provinces', [ProvinceController::class, 'index']);                // Listar provincias
$router->get('/provinces/create', [ProvinceController::class, 'createForm']);    // Mostrar formulario de creación
$router->post('/provinces/store', [ProvinceController::class, 'store']);         // Guardar nueva provincia
$router->get('/provinces/edit', [ProvinceController::class, 'editForm']);        // Mostrar formulario de edición (usa $_GET['id'])
$router->post('/provinces/update', [ProvinceController::class, 'update']);       // Actualizar provincia
$router->post('/provinces/delete', [ProvinceController::class, 'destroy']);      // Eliminar provincia

?>