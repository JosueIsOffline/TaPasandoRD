<?php

namespace App\Factories;

use App\Interfaces\OAuthProviderInterface;
use App\Providers\GoogleProvider;
use App\Providers\MicrosoftProvider;

class OAuthProviderFactory
{
  public static function create(string $provider): OAuthProviderInterface
  {
    return match ($provider) {
      'google' => new GoogleProvider(),
      'microsoft' => new MicrosoftProvider(),
      default => throw new \Exception('Provider not supported')
    };
  }
}
