<?php

namespace App\Controllers\Api;

use App\Repositories\UserRepository;
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
  public function login(): Response
  {
    $params = $this->request->getAllPost();


    if ($this->auth->attempt($params['email'], $params['password'])) {
      return $this->smartResponse([
        'message' => 'Inicia de session exitoso',
        'user' => $this->auth->user(),
        '/',
        302
      ]);
    }

    return $this->smartResponse(['error' => "Error al iniciar session"], '/login', 400);
  }

  public function register(): Response
  {
    $params = $this->request->getAllPost();

    $data = [
      'nombre' => $params['nombre'],
      'email' => $params['email'],
      'rol' => $params['rol'],
      'password' => password_hash($params['password'], PASSWORD_DEFAULT)
    ];

    $repo = new UserRepository();
    $repo->create($data);
    return $this->smartResponse(
      [
        'message' => "Usuario creado exitosamente, inicia session",
      ],
      '/login',
      200,
    );
  }
}
