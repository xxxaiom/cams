<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

class UserAccountsController extends Controller
{

    public function index()
    {
        
        $user = auth()->user()->role;

        $users = User::select('*')->where('role', 'admin')->get();

        if($user === 'superAdmin'){
            return view('content.superAdmin.accounts-user', compact('users'));
        }
        else{
            
        }
    }
}
