<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class Comment extends Model
{
  public array $fillabel = ['incident_id', 'user_id', 'content', 'active'];
}
