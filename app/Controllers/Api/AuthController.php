<?php

namespace App\Controllers\Api;

use App\Factories\OAuthProviderFactory;
use App\Interfaces\OAuthProviderInterface;
use App\Repositories\UserRepository;
use JosueIsOffline\Framework\Auth\AuthService;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class AuthController extends AbstractController
{
  private AuthService $auth;
  private OAuthProviderInterface $provider;
  private UserRepository $repoUser;

  public function __construct()
  {
    parent::__construct();
    $this->auth = new AuthService();
    $this->repoUser = new UserRepository();
  }

  public function proRedirect(string $provider)
  {
    // this load the provider needed
    $this->resolveProvider($provider);

    $authUrl = $this->provider->getAuthUrl();
    $_SESSION['oauth2state'] = $this->provider->getState();

    header('Location: ' . $authUrl);

    return $this->redirect($authUrl);
  }

  public function callback(string $provider): Response
  {
    // this load the provider needed
    $this->resolveProvider($provider);

    if (empty($_GET['state']) || ($_GET['state'] !== ($_SESSION['oauth2state'] ?? ''))) {
      unset($_SESSION['oauth2state']);
      return $this->redirect('/login');
    }

    unset($_SESSION['oauth2state']);

    if (empty($_GET['code'])) {
      return $this->redirect('/login?error=no_authorization_code');
    }

    try {
      // Get access token
      $token = $this->provider->getAccessToken($_GET['code']);

      // Get user details from Google
      $userProvider = $this->provider->getUserData($token);

      $user = $this->findOrCreateUser($userProvider, $provider);

      $this->auth->loginById($user['id']);

      return $this->redirect('/');
    } catch (\Exception $e) {
      error_log($provider . ' OAuth Error: ' . $e->getMessage());
      return $this->redirect('/login?error=oauth_failed');
    }
  }

  private function resolveProvider(string $provider): void
  {
    $this->provider = OAuthProviderFactory::create($provider);
  }

  public function findOrCreateUser(array $userProviderData, ?string $provider): array
  {
    $existingUser = $this->repoUser->findByEmail($userProviderData['email']);
    if ($existingUser) {
      $this->repoUser->update([
        'id' => $existingUser['id'],
        'supplier_auth' => $provider,
        'photo_url' => $userProviderData['picture'] ?? null
      ]);

      return $existingUser;
    }

    $userData = [
      'name' => $userProviderData['name'],
      'email' => $userProviderData['email'],
      'photo_url' => $userProviderData['picture'] ?? null,
      'role_id' => 1,
      'supplier_auth' => $provider,
      'password' => null,
    ];

    $this->repoUser->create($userData);
    $user = $this->repoUser->findByEmailAndProvider($userData['email'], $userData['supplier_auth']);

    return array_merge($userData, ['id' => $user["id"]]);
  }

  public function login(): Response
  {
    $params = $this->request->getAllPost();
    $required = $this->validateRequired($params, ['email', 'password']);

    if ($this->auth->attempt($params['email'], $params['password'])) {
      return $this->success(
        [
          'user' => $this->auth->user(),
        ],
        'Inicio de sesion exitoso',
        302,
        '/'
      );
    }

    if (empty($required['email'])) {
      $required['emailPersist'] = $params['email'];

      return $this->error($required, 'Disculpe, no se pudo iniciar sesión.');

      //----------------------------------------------------------------------------------------------------------------
      // TODO: Debemos agregar la funcionalidad de recuperación de contraseña.

      // return $this->error($required, 'No se pudo iniciar sesión. Si crees que has olvidado tus accesos recuperalos en la opción de abajo.');
    }

    // return $this->error($required, 'No se pudo iniciar sesión. Si crees que has olvidado tus accesos recupera tu contraseña en la opción de abajo.');

    //----------------------------------------------------------------------------------------------------------------

    return $this->error($required, 'Disculpe, nose pudo iniciar sesión.');
  }

  public function register(): Response
  {
    $params = $this->request->getAllPost();

    $required = $this->validateRequired($params, ['nombre', 'email', 'password', 'rol']);

    if (!empty($required)) {
      return $this->error($required, 'Por favor, complete todos los campos requeridos.');
    }

    $existingUser = $this->repoUser->findByEmail($params['email']);
    if ($existingUser) {
      $preservedData = [
        'name' => $params['nombre'] ?? '',
        'email' => $params['email'] ?? '',
        'rol' => $params['rol'] ?? ''
      ];

      return $this->error(
        array_merge(['email' => 'Este email ya está registrado'], ['preservedData' => $preservedData]),
        'El email ya está en uso. Por favor, use otro email o inicie sesión.'
      );
    }

    $data = [
      'name' => $params['nombre'],
      'email' => $params['email'],
      'role_id' => $this->getRoleId($params['rol']),
      'supplier_auth' => 'local',
      'password' => password_hash($params['password'], PASSWORD_DEFAULT)
    ];

    try {
      $this->repoUser->create($data);
      return $this->success(
        [],
        "Usuario creado exitosamente, ya podrás iniciar sesión",
        200,
        '/login',
      );
    } catch (\Exception $e) {
      if (
        strpos($e->getMessage(), 'Duplicate entry') !== false ||
        strpos($e->getMessage(), 'UNIQUE constraint failed') !== false
      ) {
        return $this->error(
          ['email' => 'Este email ya está registrado'],
          'El email ya está en uso.'
        );
      }

      error_log('Error al registrar usuario: ' . $e->getMessage());
      return $this->error([], 'Error interno del servidor. Intente nuevamente.');
    }
  }

  public function logout(): Response
  {
    $this->auth->logout();

    return $this->redirect(
      '/login',
    );
  }

  private function getRoleId(string $role): int
  {
    $roles = [
      'reportero' => 1,
      'validador' => 2,
      'admin' => 3
    ];

    return $roles[$role];
  }
}

