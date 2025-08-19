<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class Municipality extends Model
{
    protected string $table = 'municipalities';

    protected array $fillable = ['name', 'code', 'province_id' ];
}
