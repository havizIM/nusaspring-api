<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Hash;
use App\User;
use Auth;
use App\Log;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Fields Required',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('username', '=', $request->username)->firstOrFail();

        if(!$user){

            return response()->json([
                'status' => false,
                'message' => 'Username not found'
            ], 400);

        } else {

            if(!Hash::check($request->password, $user->password)){

                return response()->json([
                    'status' => false,
                    'message' => 'Wrong Password'
                ], 400);

            } else {
                
                if($user->active === 'N') {
                    return response()->json([
                        'status' => false,
                        'message' => 'User is not active'
                    ], 400);

                } else {

                    $user->generateToken();

                    try {
                        $log = new Log;
                        $log->user_id = Auth::id();
                        $log->description = 'Login System';
                        $log->reference_id = Auth::id();
                        $log->url = '#/setting';
                        $log->save();
                    } catch (\Exception $e) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Failed add log',
                            'error' => $e->getMessage()
                        ], 500);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Success Login',
                        'data' => $user
                    ], 200);

               }
            }
        }
        
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Logout System';
            $log->reference_id = Auth::id();
            $log->url = '#/setting';
            $log->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add log',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Success Logout'
        ], 200); 
    }
}
