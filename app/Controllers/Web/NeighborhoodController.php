<?php

namespace App\Controllers\Web;

use App\Repositories\NeighborhoodRepository;
use App\Repositories\MunicipalityRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class NeighborhoodController extends AbstractController
{
    protected NeighborhoodRepository $neighborhoodRepo;
    protected MunicipalityRepository $municipalityRepo;

    public function __construct()
    {
        parent::__construct();
        $this->neighborhoodRepo = new NeighborhoodRepository();
        $this->municipalityRepo = new MunicipalityRepository();
    }

    public function index(): Response
    {
        $neighborhoods = $this->neighborhoodRepo->getAllWithMunicipality();
        return $this->render('pages/neighborhoods/index.html.twig', [
            'neighborhoods' => $neighborhoods
        ]);
    }

    public function create(): Response
    {
        $municipalities = $this->municipalityRepo->getAll();
        return $this->render('pages/neighborhoods/create.html.twig', [
            'municipalities' => $municipalities
        ]);
    }

    public function store(): Response
    {
        $name = trim($_POST['name'] ?? '');
        $municipalityId = intval($_POST['municipality_id'] ?? 0);

        if ($name === '' || $municipalityId <= 0) {
            return $this->render('pages/neighborhoods/create.html.twig', [
                'error'         => 'El nombre y el municipio son obligatorios',
                'old'           => $_POST,
                'municipalities'=> $this->municipalityRepo->getAll()
            ]);
        }

        $this->neighborhoodRepo->create([
            'name'            => $name,
            'municipality_id' => $municipalityId
        ]);

        return $this->redirect('/neighborhoods');
    }

    public function edit($id): Response
    {
        $neighborhood = $this->neighborhoodRepo->findById($id);
        if (!$neighborhood) {
            return new Response('Barrio no encontrado', 404);
        }

        $municipalities = $this->municipalityRepo->getAll();

        return $this->render('pages/neighborhoods/edit.html.twig', [
            'neighborhood'  => $neighborhood,
            'municipalities'=> $municipalities
        ]);
    }

    public function update($id): Response
    {
        $name = trim($_POST['name'] ?? '');
        $municipalityId = intval($_POST['municipality_id'] ?? 0);

        if ($name === '' || $municipalityId <= 0) {
            return $this->render('pages/neighborhoods/edit.html.twig', [
                'error'         => 'El nombre y el municipio son obligatorios',
                'neighborhood'  => $this->neighborhoodRepo->findById($id),
                'municipalities'=> $this->municipalityRepo->getAll()
            ]);
        }

        $this->neighborhoodRepo->update($id, [
            'name'            => $name,
            'municipality_id' => $municipalityId
        ]);

        return $this->redirect('/neighborhoods');
    }

    public function destroy($id): Response
    {
        $this->neighborhoodRepo->delete($id);
        return $this->redirect('/neighborhoods');
    }
}
