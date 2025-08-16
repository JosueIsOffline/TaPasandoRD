<?php

namespace App\Controllers\Api;

use App\Repositories\ValidatorRepository;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class ValidatorController extends AbstractController
{

  private ValidatorRepository $vRepo;

  public function __construct()
  {
    $this->vRepo = new ValidatorRepository();
  }

  public function getPendingIncident(): Response
  {
    $data = $this->vRepo->getPendingIncidents();

    return $this->success($data, 200);
  }
}
