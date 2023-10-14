<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ingredient\AddIngRequest;
use App\Http\Requests\Ingredient\EditIngRequest;
use App\Http\Resources\IngredientResource;
use App\Models\Branch;
use App\Models\Destruction;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    use ApiResponseTrait;

    public function show(Ingredient $ingredient)
    {
        return $this->apiResponse(IngredientResource::make($ingredient),'success',200);
    }
    public function IngByBranch(Branch $branch)
    {
        $ingredients = $branch->ingredient()->get();

        return $this->apiResponse(IngredientResource::collection($ingredients),'success',200);
    }
    public function store(AddIngRequest $request)
    {
        $request->validated($request->all());

        $ingredient = Ingredient::create($request->all());

        return $this->apiResponse(new IngredientResource($ingredient),'Data Saved',201);
    }
    public function update(EditIngRequest $request,Ingredient $ingredient)
    {
        $request->validated($request->all());

        $ingredient->update($request->all());

        return $this->apiResponse(IngredientResource::make($ingredient),'Data Updated',200);
    }
    public function delete(Ingredient $ingredient)
    {
        $ingredient->delete();

        return $this->apiResponse(null,'Data Deleted',200);
    }
    public function editQty(Ingredient $ingredient,Request $request)
    {
        $unit = $request->unit;
        $ingunit = $ingredient->unit;
        if($unit == $ingunit) {
            $ingredient->update([
                'total_quantity' => $ingredient->total_quantity + $request->total_quantity,
            ]);
        }elseif(($unit == 'g' && $ingunit == 'kg') || ($unit == 'ml' && $ingunit == 'l')){
            $ingredient->update([
                'total_quantity' => $ingredient->total_quantity + ($request->total_quantity / 1000),
            ]);
        }elseif($unit == 'kg' && $ingunit == 'g' || $unit == 'l' && $ingunit == 'ml'){
            $ingredient->update([
                'total_quantity' => $ingredient->total_quantity + ($request->total_quantity * 1000),
            ]);
        }
        return $this->apiResponse(IngredientResource::make($ingredient),'Quantity Updated',200);
    }
    public function destruction(Ingredient $ingredient,Request $request)
    {
        $unit = $request->unit;
        $ingunit = $ingredient->unit;
        if ($unit == $ingunit) {
            $ingredient->update([
                'total_quantity' => $ingredient->total_quantity - $request->total_quantity,
            ]);
        }elseif(($unit == 'g' && $ingunit == 'kg') || ($unit == 'ml' && $ingunit == 'l')){
            $ingredient->update([
                'total_quantity' => $ingredient->total_quantity - ($request->total_quantity / 1000),
            ]);
        }elseif($unit == 'kg' && $ingunit == 'g' || $unit == 'l' && $ingunit == 'ml'){
            $ingredient->update([
                'total_quantity' => $ingredient->total_quantity - ($request->total_quantity * 1000),
            ]);
        }
       
        if ($ingredient) {
            Destruction::create([
                'ingredient_id' => $ingredient->id,
                'qty' => $request->total_quantity,
                'unit' => $unit,
                'branch_id' => $ingredient->branch_id
            ]);
        }
        return $this->apiResponse(IngredientResource::make($ingredient),'Quantity Updated',200);

    }
}
