<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class IncidentValidation extends Model
{
  public array $fillabel = ['incident_id', 'validator_id', 'status', 'comments'];
}
