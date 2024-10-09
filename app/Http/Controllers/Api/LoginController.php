<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\FcmToken;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
{
    $validatedData = $request->validate([
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
        'fcm_token' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'Invalid login details'
        ], 401);
    }

    $user = User::where('email', $request['email'])->firstOrFail();

    // تحديث fcm_token
    FcmToken::updateOrCreate(
        ['user_id' => $user->id],
        ['fcm_token' => $validatedData['fcm_token']]
    );

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'data' => [
            'user' => new AuthResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],
        'message' => 'User login successful',
    ], 200);
}
    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json(['massage' =>'user logout successful'],200);
    }
}
