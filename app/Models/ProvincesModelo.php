<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class Province extends Model
{
    protected string $table = 'provinces';
    protected array $fillable = ['name', 'code'];
}