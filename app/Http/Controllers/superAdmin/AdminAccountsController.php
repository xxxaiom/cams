<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminAccountsController extends Controller
{
    public function index()
    {
        
        $user = auth()->user()->role;

        $users = User::select('*')->where('role', 'admin')->get();

        if($user === 'superAdmin'){
            return view('content.superAdmin.accounts-admin', compact('users'));
        }
        else{
            
        }
    }

    public function newAdmin(Request $request)
    {
        try{
            $request->validate([
                'username' => ['required', 'string', 'max:30','unique:users'],
                'password' => [
                    'required',
                    'string',
                    'max:30', 
                    'confirmed',
                    Password::min(8)
                                                   ->letters()
                                                   ->mixedCase()
                                                   ->numbers()
                                                   ->symbols()
                    ]
            ]);

    
            $cred = [
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'admin'
            ];
    
            if($cred){
                DB::table('users')->insert($cred);
                return response()->json([
                    'code' => 0,
                ]);
            }else{
                return response()->json([
                    'code' => 1,
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'code' => 2,
                'message' => $e->getMessage()
            ]);
        }

       

    }
}
