<?php

namespace App\Http\Controllers\login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Citizen;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $userRole = auth()->user()->role;

            // Redirect based on user role
            if ($userRole === 'admin') {
                return redirect()->route('reports-admin-live-map');
            } elseif ($userRole === 'user') {
                return redirect()->route('user-dashboard');
            }
        }

        return view('content.login.login');
    }

    public function userLogin(Request $request)
    {
        try {

            $request->validate([
                'username' => ['required'],
                'password' => ['required'],
            ]);

            $cred = [
                'username' => $request->username,
                'password' => $request->password,
                'role' => 'user',
            ];


            if (Auth::attempt($cred)) {
                $user = Auth::user(); // Get the authenticated user

                Session::put(['user_role' => $user->role]);
                return response()->json([
                    'code' => 0,
                    'message' => 'Redirecting, Please wait....',
                ]);
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => 'Incorrect username or password',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 2,
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

    public function registerUser(Request $request)
    {
        try {
            $request->validate([
                'firstname' => ['required', 'string', 'max:50'],
                'middlename' => ['nullable', 'string', 'max:50'],
                'lastname' => ['required', 'string', 'max:50'],
                'gender' => ['required'],
                'birthdate' => ['required', 'date', 'before:today'],
                'number' => ['required', 'string', 'max:11', 'min:11'],
                'civil_status' => ['required'],
                'address' => ['required', 'string', 'max:100'],
                'uname' => ['required', 'string', 'max:50', 'unique:users,username'],
                'pword' => ['required', 'min:8', 'confirmed'],
            ]);


            $cred = [
                'username' => $request->uname,
                'password' => Hash::make($request->pword),
                'role' => 'user'
            ];


            if ($cred) {
                $id = User::insertGetID($cred);

                $register = [
                    'first_name' => $request->firstname,
                    'middle_name' => $request->middlename,
                    'last_name' => $request->lastname,
                    'gender' => $request->gender,
                    'birthdate' => $request->birthdate,
                    'number' => $request->number,
                    'civil_status' => $request->civil_status,
                    'address' => $request->address,
                    'account_id' => $id
                ];

                Citizen::create($register);

                return response()->json([
                    'code' => 0,
                    'message' => 'Registered Succesfully. Please login using the username and password'
                ]);
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => 'nagdaot ang database'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 2,
                'message' => $e->getMessage()
            ]);
        }
    }
}
