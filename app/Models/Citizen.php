<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    protected $table = 'citizen_info';
    protected $fillable = [
        'id',
        'account_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birthdate',
        'number',
        'civil_status',
        'prov',
        'municity',
        'bgy',
        'address',
        'created_at',
        'updated_at',
    ];
}
