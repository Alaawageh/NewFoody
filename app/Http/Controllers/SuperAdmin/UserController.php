<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function GetUserByBranch(Branch $branch)
    {
        if($branch->restaurant_id === auth()->user()->id) {
            $users = $branch->users()->get();
            return $this->apiResponse(UserResource::collection($users), 'success', 200);
        }
        return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN);

    }
    public function show(User $user)
    {
        return $this->apiResponse(UserResource::make($user),'success',200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => "required|min:8|max:24|regex:/(^[A-Za-z0-9]+$)+/",
            'user_type' => 'in:1,2,3,4',
            'branch_id' => 'integer|exists:branches,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $users = User::create(array_merge($request->except('password'),
        ['password' => bcrypt($request->password)]));

        return $this->apiResponse(new UserResource($users),'Data Saved Successfully',201); 

    }
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email',
            'password' => "required|min:8|max:24|regex:/(^[A-Za-z0-9]+$)+/",
            'user_type' => 'in:1,2,3,4',
            'branch_id' => 'integer|exists:branches,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user->update(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return $this->apiResponse(new UserResource($user), 'The user updated', 201);
  
    }
    public function delete(User $user)
    {

        $user->delete();

        return $this->apiResponse(null, 'The user deleted', 200);
    }
}
