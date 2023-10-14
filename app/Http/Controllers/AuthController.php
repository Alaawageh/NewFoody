<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Types\UserTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only(['email', 'password']))) {

            return response( ['message' => 'Incorrect email or password'] , 422);
        }

        $user = User::where('email', $request->email)->first();
        
         $user->update([
            'UUID' => Str::uuid()
        ]);
        $userAuth = auth()->user();
        return response([
            "token" =>  $user->createToken("API TOKEN")->plainTextToken,
            "UUID" => $user->UUID,
            "user" => UserResource::make($userAuth)
        ] , 200);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return response([
            'message' => 'user logout successfully'
        ] , 200);
    }
    public function getTokenByUUID(Request $request)
{
    $uuid = $request->uuid;

    if (!$uuid) {
        return response(['message' => 'UUID is required'], 422);
    }

    $user = User::where('UUID', $uuid)->first();

    if (!$user) {
        return response(['message' => ' UUID User not found'], 422);
    }

    $token = $user->createToken('API TOKEN')->plainTextToken;
    $user->update([
        'UUID' => null
    ]);

    return response(['token' => $token,'user'=> UserResource::make($user)], 200);
}
    
}
