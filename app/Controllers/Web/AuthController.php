<?php

namespace App\Controllers\Web;

use JosueIsOffline\Framework\Auth\AuthService;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class AuthController extends AbstractController
{
  private AuthService $auth;

  public function __construct()
  {
    parent::__construct();
    $this->auth = new AuthService();
  }

  public function showLogin(): Response
  {
    if ($this->auth->check()) {
      return $this->redirect('/');
    }
    return $this->renderWithFlash("login.html.twig");
  }

  public function showRegister(): Response
  {
    if ($this->auth->check()) {
      return $this->redirect('/');
    }
    return $this->renderWithFlash('register.html.twig');
  }

  public function logout(): Response
  {
    $this->auth->logout();
    return $this->redirect('/login');
  }
}
