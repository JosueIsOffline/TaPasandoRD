<?php

namespace App\Controllers\Web;

use App\Repositories\CategoryRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class CategoryController extends AbstractController
{
    protected CategoryRepository $categoryRepo;

    public function __construct()
    {
        parent::__construct(); // IMPORTANTE
        $this->categoryRepo = new CategoryRepository();
    }

    public function index(): Response
    {
        $categories = $this->categoryRepo->getAll();
        return $this->render('pages/categories/index.html.twig', ['categories' => $categories]);
    }

    public function create(): Response
    {
        return $this->render('pages/categories/create.html.twig');
    }

    public function store(): Response
    {
        $name       = trim($_POST['name'] ?? '');
        $icon_color = trim($_POST['icon_color'] ?? '');
        $icon       = trim($_POST['icon'] ?? '');
        $active     = isset($_POST['active']) ? (bool) $_POST['active'] : true;

        if ($name === '') {
            return $this->render('pages/categories/create.html.twig', [
                'error' => 'El nombre es obligatorio',
                'old'   => $_POST
            ]);
        }

        $this->categoryRepo->create([
            'name'       => $name,
            'icon_color' => $icon_color,
            'icon'       => $icon,
            'active'     => $active
        ]);

        return $this->redirect('/categories');
    }

    public function edit($id): Response
    {
        $category = $this->categoryRepo->findById($id);
        if (!$category) {
            return new Response('Categoría no encontrada', 404);
        }

        return $this->render('pages/categories/edit.html.twig', ['category' => $category]);
    }

    public function update($id): Response
    {
        $name       = trim($_POST['name'] ?? '');
        $icon_color = trim($_POST['icon_color'] ?? '');
        $icon       = trim($_POST['icon'] ?? '');
        $active     = isset($_POST['active']) ? (bool) $_POST['active'] : true;

        if ($name === '') {
            return $this->render('pages/categories/edit.html.twig', [
                'error'    => 'El nombre es obligatorio',
                'category' => $this->categoryRepo->findById($id)
            ]);
        }

        $this->categoryRepo->update($id, [
            'name'       => $name,
            'icon_color' => $icon_color,
            'icon'       => $icon,
            'active'     => $active
        ]);

        return $this->redirect('/categories');
    }

    public function destroy($id): Response
    {
        $this->categoryRepo->delete($id);
        return $this->redirect('/categories');
    }
}
?>