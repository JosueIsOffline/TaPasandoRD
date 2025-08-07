<?php

namespace  App\Controllers\Web;

use App\Models\Province;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class ProvinceController extends AbstractController
{
    // Listar provincias
    public function index(): Response
    {
        $provinces = Province::all();

        return $this->render('pages/provinces/index', [
            'provinces' => $provinces
        ]);
    }

    // Mostrar formulario de creación
    public function createForm(): Response
    {
        return $this->render('provinces/create.html.twig');
    }

    // Guardar nueva provincia
    public function store(): Response
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $province = new Province();
            $province->create([
                'name' => $_POST['name'],
                'code' => $_POST['code']
            ]);
        }

        return $this->redirect('/provinces');
    }

    // Mostrar formulario de edición
    public function editForm(): Response
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            return $this->render('errors/400.html.twig', ['message' => 'ID inválido']);
        }

        $province = Province::find((int)$id);

        if (!$province) {
            return $this->render('errors/404.html.twig', ['message' => 'Provincia no encontrada']);
        }

        return $this->render('provinces/edit.html.twig', [
            'province' => $province
        ]);
    }

    
    // Actualizar provincia
    public function update(): Response
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id'], $_POST['name'])) {
                return $this->render('errors/400.html.twig', ['message' => 'Faltan datos obligatorios']);
            }

            $province = new Province();
            $province->update([
                'id' => (int)$_POST['id'],
                'name' => trim($_POST['name']),
                'code' => trim($_POST['code'] ?? '')
            ]);
        }

        return $this->redirect('/provinces');
    }


    // Eliminar provincia
    public function destroy(): Response
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $province = new Province();
            $province->delete($_POST['id']);
        }

        return $this->redirect('/provinces');
    }
}
