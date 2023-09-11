<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Repository\AddRepositoryRequest;
use App\Http\Requests\Repository\EditRepositoryRequest;
use App\Http\Resources\RepoResource;
use App\Models\Branch;
use App\Models\Repo;
use Illuminate\Http\Request;

class RepoController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $repos = RepoResource::collection(Repo::get());
        return $this->apiResponse($repos,'success',200); 
    }

    public function getByBranch(Branch $branch)
    {
        $repos = $branch->repo()->get();
        return $this->apiResponse(RepoResource::collection($repos),'success',200);
    }

    public function show(Repo $repo)
    {
        return $this->apiResponse(RepoResource::make($repo),'success',200);
    }

    public function store(AddRepositoryRequest $request)
    {
        $request->validated($request->all());
        $repo = Repo::create($request->all());
        return $this->apiResponse(new RepoResource($repo),'Data Successfully Saved',201);
    }
    public function update(EditRepositoryRequest $request , Repo $repo)
    {
        $request->validated($request->all());
        $repo->update($request->all());
        return $this->apiResponse(RepoResource::make($repo),'Data Successfully Saved',200);
    }

    public function delete(Repo $repo)
    {
        $repo->delete();
        return $this->apiResponse(null,'Data Successfully Deleted',200);
    }

}
