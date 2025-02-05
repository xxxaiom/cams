<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserDashboardController extends Controller
{
    public function index()
    {

        return view('content.user.user-dashboard');
    }

    public function fetchBoundaries()
    {

        $boundaries = DB::table('barangay_boundaries')
            ->select('barangay_name', 'municipality_name', 'province_name', 'region_name', 'coordinates')
            ->get();

        return response()->json([
            'boundaries' => $boundaries
        ]);
    }
}
