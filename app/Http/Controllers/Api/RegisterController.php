<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'fcm_token' => 'required|string',
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
    ]);

    FcmToken::create([
        'user_id' => $user->id,
        'fcm_token' => $validatedData['fcm_token'],
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    // الحصول على fcm_token المرتبط بالمستخدم
    $fcmToken = FcmToken::select('fcm_token')->where('user_id',$user->id)->first(); // تأكد من وجود علاقة fcmToken في نموذج User

    return response()->json([
        'data' => [
            'user' => new AuthResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],
        'message' => 'User registration successful',
    ], 200);
}
}
