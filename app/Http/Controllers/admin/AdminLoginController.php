<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('content.login.admin-login');
    }

    public function adminLogin(Request $request)
    {
        try {
            $request->validate([
                'username' => ['required'],
                'password' => ['required'],
            ]);


            $cred = [
                'username' => $request->username,
                'password' => $request->password,
            ];

            if (Auth::attempt($cred)) {
                $user = Auth::user();
                if ($user->role === 'admin' || $user->role === 'superAdmin') {
                    Session::put(['user_role' => $user->role]);
                    Session::put(['user_id' => $user->id]);
                    return response()->json([
                        'code' => 0,
                        'message' => 'Redirecting, Please wait...'
                    ]);
                } else {
                    Session::forget('user_role');
                    Auth::logout();
                    return response()->json([
                        'code' => 1,
                        'message' => 'Unauthorized Access!'
                    ]);
                }
            } else {
                return response()->json([
                    'code' => 2,
                    'message' => 'Incorrect username or password',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 3,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        Session::forget('user_role');
        Auth::logout();
        return redirect()->route('login');
    }
}
