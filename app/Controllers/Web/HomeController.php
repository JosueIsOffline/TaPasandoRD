<?php

namespace App\Controllers\Web;

use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class HomeController extends AbstractController
{
  public function index(): Response
  {
    return $this->renderWithFlash('index.html.twig');
  }
}
