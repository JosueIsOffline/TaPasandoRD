<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class Category extends Model
{
    protected string $table = 'categories';

    protected array $fillable = [
        'name',
        'icon_color',
        'icon',
        'active'
    ];
}
