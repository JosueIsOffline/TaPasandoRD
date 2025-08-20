<?php 

namespace App\Repositories;

use JosueIsOffline\Framework\Database\DB;

class CategoryRepository
{
    protected string $table = 'categories';

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
                'name'       => $data['name'] ?? '',
                'icon_color' => $data['icon_color'] ?? null,
                'icon'       => $data['icon'] ?? null,
                'active'     => $data['active'] ?? true
            ]);
    }

    public function update(int $id, array $data)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'name'       => $data['name'] ?? '',
                'icon_color' => $data['icon_color'] ?? null,
                'icon'       => $data['icon'] ?? null,
                'active'     => $data['active'] ?? true
            ]);
    }

    public function delete(int $id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete();
    }
}
