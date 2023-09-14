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

class UserController extends Controller
{
    use ApiResponseTrait;

    public function GetUserByBranch(Branch $branch)
    {
        $users = $branch->users()->get();
        return $this->apiResponse($users, 'success', 200);
    }
    public function show(User $user)
    {
        return $this->apiResponse(UserResource::make($user),'success',200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/(^[A-Za-z ]+$)+/|between:2,100',
            'email' => 'required|email|unique:users',
            'password' => "required|min:8|max:24|regex:/(^[A-Za-z0-9]+$)+/",
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
            'name' => 'regex:/(^[A-Za-z ]+$)+/|between:2,100',
            'email' => 'email|unique:users',
            'password' => "min:8|max:24|regex:/(^[A-Za-z0-9]+$)+/",
            'branch_id' => 'exists:branches,id'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }
        if ($user)
        {
            if($request->query()){
                return response()->json(null, 'Error');
            }else{
                $user->update(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
    
                return $this->apiResponse(new UserResource($user), 'The user updated', 201);
            }

        }
    }
    public function destroy(User $user)
    {
        $user->delete();

        return $this->apiResponse(null, 'The user deleted', 200);
    }
}
