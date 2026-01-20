<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        // validation rules
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:6',
            'confirm_password'  => 'required|same:password'
        ]);

        // check validation
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Validation Fails",
                "data" => $validator->errors()->all()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $response = [];
        $response['token'] = $user->createToken("MyApp")->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;

        // success response (later you can create user here)
        return response()->json([
            "status" => 1,
            "message" => "User Registered",
            "data" => $response
        ]);
    }
    public function login(Request $request)
    {
     
        // validation rules
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6'
        ]);

        // check validation
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Validation Fails",
                "data" => $validator->errors()->all()
            ], 422);
        }

        // find user
        $user = User::where('email', $request->email)->first();

        // check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "status" => 0,
                "message" => "Invalid Credentials"
            ], 401);
        }

        // create new token
        $token = $user->createToken("MyApp")->plainTextToken;

        $response = [];
        $response['token'] = $token;
        $response['name'] = $user->name;
        $response['email'] = $user->email;

        return response()->json([
            "status" => 1,
            "message" => "Login Successful",
            "data" => $response
        ]);
    }
}
