<?php

namespace App\Providers;

use App\Interfaces\OAuthProviderInterface;
use League\OAuth2\Client\Provider\Google;

class GoogleProvider implements OAuthProviderInterface
{

  private Google $provider;

  public function __construct()
  {
    $this->provider = new Google([
      'clientId' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
      'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
      'redirectUri' => $_ENV['GOOGLE_REDIRECT_URI'] ?? ''
    ]);
  }
  public function getAuthUrl(): string
  {
    return $this->provider->getAuthorizationUrl([
      'scope' => ['openid', 'email', 'profile']
    ]);
  }

  public function getAccessToken(string $code)
  {
    return $this->provider->getAccessToken('authorization_code', [
      'code' => $code
    ]);
  }

  public function getUserData($token): array
  {
    $googleUser = $this->provider->getResourceOwner($token);

    $user = $googleUser->toArray();

    return $user;
  }

  public function getState(): string
  {
    return $this->provider->getState();
  }
}
