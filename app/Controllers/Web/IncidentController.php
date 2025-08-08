<?php

namespace App\Controllers\Web;

use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class IncidentController extends AbstractController
{
  public function index(): Response
  {
    return $this->renderWithFlash('incident/index.html.twig');
  }
}
