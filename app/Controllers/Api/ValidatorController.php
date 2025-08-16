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
    $pendings = $this->vRepo->getPendingIncidents();

    return $this->success($pendings, 200);
  }
}
