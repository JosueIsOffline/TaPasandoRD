<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class User extends Model
{
  public array $fillabel = ['name', 'email', 'photo_url', 'rol', 'proveedor_auth', 'password', 'active'];
}
