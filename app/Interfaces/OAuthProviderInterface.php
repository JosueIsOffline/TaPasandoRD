<?php

namespace App\Interfaces;

interface OAuthProviderInterface
{
  public function getAuthUrl(): string;
  public function getAccessToken(string $code);
  public function getUserData($token): array;
  public function getState(): string;
}
