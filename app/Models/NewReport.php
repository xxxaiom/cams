<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewReport extends Model
{
    protected $table = 'user_reports';
    protected $fillable = [
        'id',
        'user_id',
        'crime_types_id',
        'crime_location',
        'crime_description',
        'latitude',
        'longitude',
        'accuracy',
        'status',
        'created_at',
        'updated_at',
    ];
    use HasFactory;
}
