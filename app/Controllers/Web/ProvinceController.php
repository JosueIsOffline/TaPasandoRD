<?php

namespace App\Controllers\Web;

use App\Repositories\ProvinceRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class ProvinceController extends AbstractController
{
  protected ProvinceRepository $provinceRepo;

  public function __construct()
  {
    parent::__construct();  // IMPORTANTE
    $this->provinceRepo = new ProvinceRepository();
  }

  public function index(): Response
  {
    $provinces = $this->provinceRepo->getAll();
    return $this->render('administration-panel/provinces/index.html.twig', ['provinces' => $provinces]);
  }

  public function create(): Response
  {
    return $this->render('administration-panel/provinces/create.html.twig');
  }

  public function store(): Response
  {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if ($name === '') {
      return $this->render('administration-panel/provinces/create.html.twig', [
        'error' => 'El nombre es obligatorio',
        'old'   => $_POST
      ]);
    }

    $this->provinceRepo->create(['name' => $name, 'code' => $code]);
    return $this->redirect('/provinces');
  }

  public function edit($id): Response
  {
    $province = $this->provinceRepo->findById($id);
    if (!$province) {
      return new Response('Provincia no encontrada', 404);
    }

    return $this->render('administration-panel/provinces/edit.html.twig', ['province' => $province]);
  }

  public function update($id): Response
  {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if ($name === '') {
      return $this->render('administration-panel/provinces/edit.html.twig', [
        'error'    => 'El nombre es obligatorio',
        'province' => $this->provinceRepo->findById($id)
      ]);
    }

    $this->provinceRepo->update($id, ['name' => $name, 'code' => $code]);
    return $this->redirect('/provinces');
  }

  public function destroy($id): Response
  {
    $this->provinceRepo->delete($id);
    return $this->redirect('/provinces');
  }
}
