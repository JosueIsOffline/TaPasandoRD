<?php

namespace App\Controllers\Web;

use App\Strategies\GetAllDataConfiguration;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class MapController extends AbstractController
{

  public function index(): Response
  {
    $strategie = new GetAllDataConfiguration();
    $dataConfiguration = $strategie->GetAllData();

    return $this->renderWithFlash("/map/index.html.twig", $dataConfiguration);
  }
}
