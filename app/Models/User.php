<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class User extends Model
{
  public array $fillabel = ['nombre', 'email', 'foto', 'rol', 'proveedor_auth', 'password', 'activo', 'creado_en'];
}
