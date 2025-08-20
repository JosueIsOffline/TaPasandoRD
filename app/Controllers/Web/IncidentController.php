<?php

namespace App\Controllers\Web;

use App\Strategies\GetAllDataConfiguration;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class IncidentController extends AbstractController
{
  public function index(): Response
  {
    $strategie = new GetAllDataConfiguration();
    $dataConfiguration = $strategie->GetAllData();
    return $this->renderWithFlash('incident/index.html.twig', $dataConfiguration);
  }

  public function list(): Response
  {
    return $this->renderWithFlash('incident/list.html.twig');
  }
}
