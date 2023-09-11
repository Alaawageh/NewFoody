<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request)
    {
        // $request->validated($request->all());

        $users = User::create(array_merge($request->except('password'),
            ['password' => bcrypt($request->password)]));

        return $this->apiResponse(new UserResource($users),'Data Saved Successfully',201);
    }
}
