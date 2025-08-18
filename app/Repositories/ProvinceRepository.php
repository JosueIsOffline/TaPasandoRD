<?php

namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository
{
    public function getAll(): ?array
    {
        $province = new Province();
        $provinces = $province->query()->get();
        return $provinces;
    }

    public function getById(int $id): ?array
    {
        return Province::query()->where('id', $id)->first();
    }

    public function create(array $data): void
    {
        Province::query()->insert($data);
    }

    public function update(array $data): void
    {
        Province::query()->where('id', $data['id'])->update($data);
    }

    public function delete(int $id): void
    {
        Province::query()->where('id', $id)->delete();
    }
}
