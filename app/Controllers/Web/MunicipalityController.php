<?php

namespace App\Controllers\Web;

use App\Repositories\MunicipalityRepository;
use App\Repositories\ProvinceRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class MunicipalityController extends AbstractController
{
  protected MunicipalityRepository $municipalityRepo;
  protected ProvinceRepository $provinceRepo;

  public function __construct()
  {
    parent::__construct(); // IMPORTANTE
    $this->municipalityRepo = new MunicipalityRepository();
    $this->provinceRepo = new ProvinceRepository();
  }

  public function index(): Response
  {
    $municipalities = $this->municipalityRepo->getAllWithProvince();
    return $this->render('administration-panel/municipalities/index.html.twig', [
      'municipalities' => $municipalities
    ]);
  }

  public function create(): Response
  {
    $provinces = $this->provinceRepo->getAll();
    return $this->render('administration-panel/municipalities/create.html.twig', [
      'provinces' => $provinces
    ]);
  }

  public function store(): Response
  {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $provinceId = intval($_POST['province_id'] ?? 0);

    if ($name === '' || $provinceId <= 0) {
      return $this->render('administration-panel/municipalities/create.html.twig', [
        'error'     => 'El nombre y la provincia son obligatorios',
        'old'       => $_POST,
        'provinces' => $this->provinceRepo->getAll()
      ]);
    }

    $this->municipalityRepo->create([
      'name'        => $name,
      'code'        => $code,
      'province_id' => $provinceId
    ]);

    return $this->redirect('/municipalities');
  }

  public function edit($id): Response
  {
    $municipality = $this->municipalityRepo->findById($id);
    if (!$municipality) {
      return new Response('Municipio no encontrado', 404);
    }

    $provinces = $this->provinceRepo->getAll();

    return $this->render('administration-panel/municipalities/edit.html.twig', [
      'municipality' => $municipality,
      'provinces'    => $provinces
    ]);
  }

  public function update($id): Response
  {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $provinceId = intval($_POST['province_id'] ?? 0);

    if ($name === '' || $provinceId <= 0) {
      return $this->render('administration-panel/municipalities/edit.html.twig', [
        'error'        => 'El nombre y la provincia son obligatorios',
        'municipality' => $this->municipalityRepo->findById($id),
        'provinces'    => $this->provinceRepo->getAll()
      ]);
    }

    $this->municipalityRepo->update($id, [
      'name'        => $name,
      'code'        => $code,
      'province_id' => $provinceId
    ]);

    return $this->redirect('/municipalities');
  }

  public function destroy($id): Response
  {
    $this->municipalityRepo->delete($id);
    return $this->redirect('/municipalities');
  }
}
