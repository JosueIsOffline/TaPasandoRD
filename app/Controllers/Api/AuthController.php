<?php

namespace App\Controllers\Api;

use App\Repositories\UserRepository;
use JosueIsOffline\Framework\Auth\AuthService;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;
use League\OAuth2\Client\Provider\Google;

class AuthController extends AbstractController
{
  private AuthService $auth;
  private Google $provider;
  private UserRepository $repoUser;

  public function __construct()
  {
    parent::__construct();
    $this->auth = new AuthService();
    $this->repoUser = new UserRepository();

    $this->provider = new Google([
      'clientId'     => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
      'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
      'redirectUri'  => $_ENV['GOOGLE_REDIRECT_URI'] ?? '',
    ]);
  }

  public function proRedirect(): Response
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $authUrl = $this->provider->getAuthorizationUrl([
      'scope' => ['openid', 'email', 'profile']
    ]);

    $_SESSION['oauth2state'] = $this->provider->getState();


    return $this->redirect($authUrl);
  }

  public function callback(): Response
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    if (empty($_GET['state']) || ($_GET['state'] !== ($_SESSION['oauth2state'] ?? ''))) {
      unset($_SESSION['oauth2state']);
      return $this->redirect('/login');
    }

    unset($_SESSION['oauth2state']);

    error_log('Estado recibido: ' . ($_GET['state'] ?? 'n/a'));
    error_log('Estado esperado: ' . ($_SESSION['oauth2state'] ?? 'n/a'));


    if (empty($_GET['code'])) {
      return $this->redirect('/login?error=no_authorization_code');
    }

    try {
      // Get access token
      $token = $this->provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
      ]);

      // Get user details from Google
      $googleUser = $this->provider->getResourceOwner($token);
      $googleUserData = $googleUser->toArray();

      $user = $this->findOrCreateUser($googleUserData);

      $this->auth->loginById($user['id']);

      return $this->redirect('/');
    } catch (\Exception $e) {
      // Handle error
      error_log('Google OAuth Error: ' . $e->getMessage());
      return $this->redirect('/login?error=oauth_failed');
    }
  }

  public function findOrCreateUser(array $googleData): array
  {
    $existingUser = $this->repoUser->findByEmail($googleData['email']);

    if ($existingUser) {
      $this->repoUser->update([
        'id' => $existingUser['id'],
        'proveedor_auth' => 'google',
        'foto' => $googleData['picture'] ?? null
      ]);

      return $existingUser;
    }

    $userData = [
      'nombre' => $googleData['name'],
      'email' => $googleData['email'],
      'foto' => $googleData['picture'] ?? null,
      'rol' => 'reportero',
      'proveedor_auth' => 'google',
      'password' => null,
    ];

    $this->repoUser->create($userData);
    $user = $this->repoUser->findByEmailAndProvider($userData['email'], $userData['proveedor_auth']);

    return array_merge($userData, ['id' => $user["id"]]);
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
