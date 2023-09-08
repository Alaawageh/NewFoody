<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddBranchRequest;
use App\Http\Requests\EditBranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use ApiResponseTrait; 

    public function index()
    {
        $branch = BranchResource::collection(Branch::get());
        return $this->apiResponse($branch,'success',200);
    }

    public function show(Branch $branch)
    {
        return $this->apiResponse(BranchResource::make($branch), 'success', 200);
    }
    
    public function getBranches(Restaurant $restaurant)
    {
        $branches = $restaurant->branch()->get();
        return $this->apiResponse(BranchResource::collection($branches),'success',200);
    }

    public function store(AddBranchRequest $request)
    {
        $request->validated($request->all());

        $branch = Branch::create(array_merge(
            $request->except('password'),
            ['password' => bcrypt($request->password)]
        ));
        return $this->apiResponse(new BranchResource($branch),'Data successfully saved',201);
    }

    public function update(EditBranchRequest $request ,Branch $branch)
    {
        $request->validated($request->all()); 

        $branch->update(array_merge(
            $request->except('password'),
            ['password' => bcrypt($request->password)]
        ));

        return $this->apiResponse(BranchResource::make($branch),'Data successfully saved',200);

    }

    public function delete(Branch $branch)
    {
        $branch->delete();
        
        return $this->apiResponse(null, 'Deleted Successfully', 200);
    }
}
