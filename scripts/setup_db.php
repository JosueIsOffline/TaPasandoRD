<?php

use JosueIsOffline\Framework\Database\DB;

require_once __DIR__ . '/../vendor/autoload.php';

// cargar configuración
$config = json_decode(file_get_contents(__DIR__ . '/../config/database.json'), true);
DB::configure($config);

// funcion para ejecutar multiple sentencias de un archivo SQL
function ejecutarArchivoSQL(string $ruta): void
{
    if (!file_exists($ruta)) {
        exit("No se encontro el archivo: $ruta\n");
    }

    $sql = file_get_contents($ruta);
    $sentencias = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));

    foreach ($sentencias as $s) {
        try {
            DB::raw($s);
        } catch (Throwable $e) {
            echo "Error en:\n $s \n { $e->getMessage() }\n \n";
        }
    }
}

// Ejecutar estructura y seed
echo "Ejecutando estructura...\n";
ejecutarArchivoSQL(__DIR__ . '/incidents_db.sql');

echo "Insertando datos de prueba...\n";
ejecutarArchivoSQL(__DIR__ . '/seed.sql');

echo "Base de datos inicializada correctamente.\n";
