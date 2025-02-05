<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrimeType extends Model
{
    protected $table = 'crime_table';
    protected $fillable = 
    [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
}
