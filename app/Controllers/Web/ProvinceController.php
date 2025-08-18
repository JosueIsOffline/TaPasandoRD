<?php

namespace App\Controllers\Web;

use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class ProvinceController extends AbstractController
{
  public function index(): Response
  {
    return $this->renderWithFlash('province/index.html.twig');
  }

  public function list(): Response
  {
    return $this->renderWithFlash('province/list.html.twig');
  }
}