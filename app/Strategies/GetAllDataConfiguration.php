<?php

namespace App\Strategies;

use App\Repositories\CategoryRepository;
use App\Repositories\MunicipalityRepository;
use App\Repositories\NeighborhoodRepository;
use App\Repositories\ProvinceRepository;

class GetAllDataConfiguration
{
  private ProvinceRepository $pRepo;
  private NeighborhoodRepository $nRepo;
  private MunicipalityRepository $mRepo;
  private CategoryRepository $cRepo;

  public function __construct()
  {
    $this->pRepo = new ProvinceRepository();
    $this->nRepo = new NeighborhoodRepository();
    $this->mRepo = new MunicipalityRepository();
    $this->cRepo = new CategoryRepository();
  }


  public function GetAllData(): array
  {

    $data = [
      'provinces' => $this->pRepo->getAll(),
      'neighborhoods' => $this->nRepo->getAll(),
      'municipalities' => $this->mRepo->getAll(),
      'categories' => $this->cRepo->getAll(),
    ];

    return $data;
  }
}
