<?php

namespace App\Controllers\Web;

use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class AdministrationController extends AbstractController
{
  public function index(): Response
  {
    return $this->renderWithFlash('administration-panel/index.html.twig');
  }
}
