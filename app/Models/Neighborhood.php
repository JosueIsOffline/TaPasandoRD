<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class Neighborhood extends Model
{
    protected string $table = 'neighborhoods';

    protected array $fillable = ['name', 'municipality_id'];
}
