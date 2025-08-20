<?php

namespace App\Repositories;

use JosueIsOffline\Framework\Database\DB;

class MunicipalityRepository
{
    protected string $table = 'municipalities';

    public function getAll()
    {
        return DB::table($this->table)
            ->select()
            ->get();
    }
    
    public function getAllWithProvince()
    {
    $sql = "
        SELECT m.*, p.name AS province_name
        FROM municipalities m
        INNER JOIN provinces p ON m.province_id = p.id";

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
                'name'        => $data['name'] ?? '',
                'code'        => $data['code'] ?? '',
                'province_id' => $data['province_id'] ?? null
            ]);
    }

    public function update(int $id, array $data)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'name'        => $data['name'] ?? '',
                'code'        => $data['code'] ?? '',
                'province_id' => $data['province_id'] ?? null
            ]);
    }

    public function delete(int $id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete();
    }
}
