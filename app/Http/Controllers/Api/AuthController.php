<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'password'=>bcrypt($validated['password']),
        ]);

        $token = $user->createToken('DeveloperToken')->accessToken;

        // return response()->json(['token'=>$token]);
                return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $token
            ],
        ], 201);

    }

    public function login(Request $request){

        $credentials = $request->only('email','password');

        if(Auth::attempt($credentials)){
            $developer = Auth::user();
            $token = $developer->createToken('DeveloperToken')->accessToken;

            return response()->json([
            'message' => 'User login success',
            'token' => $token
            ], 200);
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

     public function user()
    {
        return response()->json(Auth::user());
    }

    public function logout(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Revoke the user's access token
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
