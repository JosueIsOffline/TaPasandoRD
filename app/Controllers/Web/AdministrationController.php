<?php

namespace App\Controllers\Web;

use App\Strategies\GetAllDataConfiguration;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class AdministrationController extends AbstractController
{
  public function index(): Response
  {
    $confService = new GetAllDataConfiguration();
    $dataConfiguration = $confService->GetAllData();

    return $this->renderWithFlash('administration-panel/index.html.twig', $dataConfiguration);
  }
}
