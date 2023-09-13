<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExtraIng\AddExtraIngRequest;
use App\Http\Requests\ExtraIng\EditExtraIngRequest;
use App\Http\Requests\ExtraIngredient\EditExtraRequest;
use App\Http\Resources\ExtraIngResource;
use App\Models\ExtraIngredient;
use Illuminate\Http\Request;

class ExtraIngController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $ExtraIngredients = ExtraIngResource::collection(ExtraIngredient::get());
        return $this->apiResponse($ExtraIngredients,'success',200);
    }
    public function show(ExtraIngredient $ExtraIngredient)
    {
        return $this->apiResponse(ExtraIngResource::make($ExtraIngredient),'success',200);
    }

    public function store(AddExtraIngRequest $request)
    {
        $request->validated($request->all());

        $ExtraIngredient = ExtraIngredient::create($request->all());

        return $this->apiResponse(new ExtraIngResource($ExtraIngredient),'Data Saved',201);
    }
    public function update(EditExtraIngRequest $request,ExtraIngredient $ExtraIngredient)
    {
        $request->validated($request->all());

        $ExtraIngredient->update($request->all());

        return $this->apiResponse(ExtraIngResource::make($ExtraIngredient),'Data Updated',200);
    }
    public function delete(ExtraIngredient $ExtraIngredient)
    {
        $ExtraIngredient->delete();

        return $this->apiResponse(null,'Data Deleted',200);
    }
}
