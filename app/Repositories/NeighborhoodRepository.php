<?php

namespace App\Repositories;

use JosueIsOffline\Framework\Database\DB;

class NeighborhoodRepository
{
    protected string $table = 'neighborhoods';

    public function getAll()
    {
        return DB::table($this->table)
            ->select()
            ->get();
    }

    public function getAllWithMunicipality()
    {
        $sql = "
            SELECT n.*, m.name AS municipality_name
            FROM neighborhoods n
            INNER JOIN municipalities m ON n.municipality_id = m.id
        ";

        return DB::raw($sql);
    }

    public function findById(int $id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data)
    {
        return DB::table($this->table)
            ->insert([
                'name'            => $data['name'] ?? '',
                'municipality_id' => $data['municipality_id'] ?? null
            ]);
    }

    public function update(int $id, array $data)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'name'            => $data['name'] ?? '',
                'municipality_id' => $data['municipality_id'] ?? null
            ]);
    }

    public function delete(int $id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete();
    }
}
