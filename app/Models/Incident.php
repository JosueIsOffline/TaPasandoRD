<?php

namespace App\Models;

use JosueIsOffline\Framework\Model\Model;

class Incident extends Model
{
  public array $fillabel = [
    'occurrence_date',
    'title',
    'description',
    'latitude',
    'longitude',
    'deaths',
    'injuries',
    'estimated_loss',
    'social_media_url',
    'photo_url',
    'status',
    'validation_date',
    'province_id',
    'municipality_id',
    'neighborhood_id',
    'category_id',
    'reported_by',
    'validated_by'
  ];
}
