<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExtraIngredient\AddExtraRequest;
use App\Http\Requests\ExtraIngredient\EditExtraRequest;
use App\Http\Resources\ExtraIngredientResource;
use App\Models\ExtraIngredient;
use App\Models\Repo;
use Illuminate\Http\Request;

class ExtraIngredientController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $extra = ExtraIngredientResource::collection(ExtraIngredient::get());

        return $this->apiResponse($extra,'success',200); 
    }
    public function show(ExtraIngredient $extraIngredient)
    {
        return $this->apiResponse(ExtraIngredientResource::make($extraIngredient),'success',200);
    }
    public function getExtra(Repo $repo)
    {
        $extra = $repo->extraIngredient()->get();

        return $this->apiResponse(ExtraIngredientResource::collection($extra),'success',200);
    }

    public function store(AddExtraRequest $request)
    {
        $request->validated($request->all());

        $extra = ExtraIngredient::create($request->all());

        return $this->apiResponse(new ExtraIngredientResource($extra),'Data successfully Saved',201);
    }
    public function update(EditExtraRequest $request , ExtraIngredient $extraIngredient)
    {
        $request->validated($request->all());

        $extraIngredient->update($request->all());

        return $this->apiResponse(ExtraIngredientResource::make($extraIngredient),'Data Updated',200);
    }
    public function delete(ExtraIngredient $extraIngredient)
    {
        $extraIngredient->delete();

        return $this->apiResponse(null,'Data Deleted',200);
    }
}
