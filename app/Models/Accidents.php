<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accidents extends Model
{
    protected $table = 'accident';
    protected $fillable = [
        'id',
        'location_id',
        'accident',
        'stageoffelony',
        'offense',
        'offenseType',
        'dateReported',
        'timeReported',
        'dateCommitted',
        'timeCommitted',
    ];
    use HasFactory;
}
