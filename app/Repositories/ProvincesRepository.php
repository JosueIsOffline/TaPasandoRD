<?php

namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository
{
    /**
     * Obtener todas las provincias
     */
    public function all(): array
    {
        return Province::all();
    }

    /**
     * Buscar una provincia por ID
     */
    public function find(int $id): ?Province
    {
        return Province::find($id);
    }

    /**
     * Crear una nueva provincia
     */
    public function create(array $data): bool
    {
        $province = new Province();
        return $province->create($data);
    }

    /**
     * Actualizar una provincia existente
     */
    public function update(int $id, array $data): bool
    {
        $province = new Province();
        return $province->update(array_merge($data, ['id' => $id]));
    }

    /**
     * Eliminar una provincia
     */
    public function delete(int $id): bool
    {
        $province = new Province();
        return $province->delete($id);
    }
}

