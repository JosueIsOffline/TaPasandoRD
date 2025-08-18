<?php

namespace App\Controllers\Api;

use App\Repositories\ProvinceRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class ProvinceController extends AbstractController
{
    private ProvinceRepository $pRepo;

    public function __construct()
    {
        $this->pRepo = new ProvinceRepository();
    }

    // Obtener todas las provincias
    public function getProvinces(): Response
    {
        $provinces = $this->pRepo->getAll();
        return $this->success($provinces);
    }

    // Obtener provincia por ID
    public function getProvinceById(int $id): Response
{
    $province = $this->pRepo->getById($id);
    if (!$province) {
        return $this->error(['message' => 'Provincia no encontrada'], 404);
    }
    return $this->success($province);
}


    // Crear una nueva provincia
    public function createProvince(): Response
    {
        $params = $this->request->getAllPost();

        // Normalizar datos simples
        $params['name'] = trim($params['name']);
        $params['code'] = trim($params['code']);

        $this->pRepo->create($params);

        return $this->success([], 'Provincia creada exitosamente.', 201, '/provinces');
    }

    // Actualizar una provincia
    public function updateProvince(): Response
    {
        $params = $this->request->getAllPost();

        $params['id'] = (int)$params['id'];
        $params['name'] = trim($params['name']);
        $params['code'] = trim($params['code']);

        $this->pRepo->update($params);

        return $this->success([], 'Provincia actualizada.', 200, null);
    }

    // Eliminar una provincia
    public function deleteProvince(): Response
    {
        $id = (int)$this->request->getPostParams('id');

        $this->pRepo->delete($id);

        return $this->success([], 'Provincia eliminada.', 200, null);
    }
}
