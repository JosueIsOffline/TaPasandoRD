<?php

namespace App\Providers;

use App\Interfaces\OAuthProviderInterface;
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;
use TheNetworg\OAuth2\Client\Provider\Azure;

class MicrosoftProvider implements OAuthProviderInterface
{

  private Azure $provider;

  public function __construct()
  {
    $this->provider = new Azure([
      'clientId' => $_ENV['MICROSOFT_CLIENT_ID'],
      'clientSecret' => $_ENV['MICROSOFT_CLIENT_SECRET'],
      'redirectUri' => $_ENV['MICROSOFT_REDIRECT_URI'],
      'defaultEndPointVersion' => '2.0'
    ]);
  }

  public function getAuthUrl(): string
  {
    $baseGraphUri = $this->provider->getRootMicrosoftGraphUri(null);
    $this->provider->scope = 'openid profile email offline_access ' . $baseGraphUri . '/User.Read';
    return $this->provider->getAuthorizationUrl([
      'scope' => $this->provider->scope
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
    $microsoftUser = $this->provider->getResourceOwner($token);

    $user = $microsoftUser->toArray();

    return $user;
  }

  public function getState(): string
  {
    return $this->provider->getState();
  }
}
