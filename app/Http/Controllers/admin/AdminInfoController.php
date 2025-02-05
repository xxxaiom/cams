<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminInfo;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminInfoController extends Controller
{
    public function index()
    {
        $details = AdminInfo::select('*')->where('account_id', auth()->user()->id)->first();
        return view('content.admin.admin-info', compact('details'));
    }

    public function updateAdminDetails(Request $request)
    {

        try{
            $request->validate([
                'firstName' => ['required', 'string', 'max:50'],
                'middleName' => ['nullable', 'string', 'max:50'],
                'lastName' => ['required', 'string', 'max:50'],
                'suffix' => ['nullable', 'string', 'max:50'],
                'gender' => ['required', 'string', 'max:50', 'in:Male,Female'],
                'birthDate' => ['required', 'date', 'before:today'],
                'phoneNumber' => ['required', 'numeric', 'digits:11', 'regex:/^09\d{9}$/'],
                'civil_status' => ['required', 'string', 'max:50', 'in:Single,Married,Divorced,Widowed'],
                'address' => ['required', 'string', 'max:100'],
            ], [
                'phoneNumber.regex' => 'Phone number should be 11 digits and starts with 09',
                'gender.in' => 'Choose only male and female',
                'civil_status.in' => 'Choose only in of the choices'    
            ]);

            $data = [
                'account_id' => auth()->user()->id,
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'suffix' => $request->suffix,
                'gender' => $request->gender,
                'birthdate' => $request->birthDate,
                'number' => $request->phoneNumber,
                'civil_status' => $request->civil_status,
                'address' => $request->address,
            ];

            $existingInfo = AdminInfo::where('account_id', auth()->user()->id)->first();

            if($existingInfo){
                $isDataChanged = false;

                foreach ($data as $key => $value) {
                    if($existingInfo->$key !== $value){
                        $isDataChanged = true;
                        break;
                    }
                }

                if($isDataChanged){
                    $existingInfo->update($data);
                    return response()->json([
                        'code' => 2,
                        'message' => 'Details updated successfully!',
                    ]);
                }
                else{
                    return response()->json([
                        'code' => 3,
                        'message' => 'No changes detected!',
                    ]);
                }
                
            }else{
                AdminInfo::create($data);
                return response()->json([
                    'code' => 0,
                    'message' => 'Details added successfully!',
                ]);
            }


        }
        catch(Exception $e){
            return response()->json([
                'code' => 1,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getOldPassword(Request $request)
    {
        $oldPassword = $request->password;

        $userId = auth()->user()->id;

        $user = DB::table('users')->select('username', 'password')->where('id', '=', $userId)->first();

        if (Hash::check($oldPassword, $user->password)) {
            return response()->json([
                'code' => 0,
            ]);
        } else {
            return response()->json([
                'code' => 1,
            ]);
        }
    }

    public function confirmNewPassword(Request $request)
    {
        $newPass = $request->newPass;
        $confirmPass = $request->confirmPass;

        if ($newPass === $confirmPass) {
            return response()->json([
                'code' => 0,
            ]);
        } else {
            return response()->json([
                'code' => 1,
            ]);
        }
    }

    public function changeAdminPassword(Request $request)
    {
        try {
            $oldPass = $request->oldPass;
            $newpassword = $request->password;
            $retypenewpassword = $request->password_confirmation;


            $request->validate([
                'password' => ['required', 'string', 'max: 50'],
                'password_confirmation' => ['required', 'string', 'max: 50'],
            ]);


            $currentPassword = DB::table('users')
                ->select('password')
                ->where('id', '=', auth()->user()->id)
                ->first();

            if (Hash::check($retypenewpassword, $currentPassword->password)) {
                return response()->json([
                    'code' => 0,
                ]);
            } elseif ($newpassword != $retypenewpassword) {
                return response()->json([
                    'code' => 1,
                ]);
            } else if($newpassword === $retypenewpassword && Hash::check($oldPass, $currentPassword->password)) {
                User::where('id', '=', auth()->user()->id)
                    ->update(['password' => Hash::make($retypenewpassword)]);

                return response()->json([
                    'code' => 2,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 3,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
