<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAdminDashbaord extends Controller
{
    public function index()
    {
        return view('content.superAdmin.superAdmin-dashboard');
    }
}
