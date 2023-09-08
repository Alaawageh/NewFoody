<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Types\UserTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only(['email', 'password']))) {

            return response( ['message' => 'obbs , we are not able to log you in , you password or email is wrong'] , 422);
        }

        $user = User::where('email', $request->email)->first();
        $userAuth = auth()->user();
        return response([
            "token" =>  $user->createToken("API TOKEN")->plainTextToken,
            "user" => UserResource::make($userAuth)
        ] , 200);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return response([
            'message' => 'user logout successfully'
        ] , 200);
    }
}
