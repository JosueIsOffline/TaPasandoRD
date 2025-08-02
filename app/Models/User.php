<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class User extends Model
{
  public array $fillabel = ['nombre', 'email', 'foto', 'rol', 'proveedor_auth', 'password', 'activo', 'creado_en'];

  protected array $hidden = ['password'];

  /**
   * Check if user is OAuth user (no password)
   */
  public function isOAuthUser(): bool
  {
    return !empty($this->attributes['proveedor_auth']) &&
      empty($this->attributes['password']);
  }

  /**
   * Get user's avatar URL
   */
  public function getAvatarUrl(): string
  {
    if (!empty($this->attributes['foto'])) {
      return $this->attributes['foto'];
    }

    // Fallback to Gravatar
    $email = $this->attributes['email'] ?? '';
    $hash = md5(strtolower(trim($email)));
    return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=150";
  }
}
