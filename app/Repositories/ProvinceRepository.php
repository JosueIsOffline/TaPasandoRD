<?php

namespace App\Repositories;

use JosueIsOffline\Framework\Database\DB;

class ProvinceRepository
{
    protected string $table = 'provinces';

    public function getAll()
    {
        return DB::table($this->table)
            ->select()
            ->get();
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
                'name' => $data['name'] ?? '',
                'code' => $data['code'] ?? ''
            ]);
    }

    public function update(int $id, array $data)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'name' => $data['name'] ?? '',
                'code' => $data['code'] ?? ''
            ]);
    }

    public function delete(int $id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete();
    }
}


