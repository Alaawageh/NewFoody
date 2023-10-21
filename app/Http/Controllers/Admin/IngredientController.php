<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ingredient\AddIngRequest;
use App\Http\Requests\Ingredient\EditIngRequest;
use App\Http\Requests\Ingredient\EditQTYRequest;
use App\Http\Resources\AdditionResource;
use App\Http\Resources\IngredientResource;
use App\Models\Addition;
use App\Models\Branch;
use App\Models\Destruction;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $this->addition($ingredient,$request);
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
    private function calculateNewQuantity(EditQTYRequest $request, Ingredient $ingredient)
    {
        $unit = $request->unit;
        $ingunit = $ingredient->unit;
        $totalQuantity = $request->total_quantity;

        if ($unit == $ingunit) {
            return $ingredient->total_quantity + $totalQuantity;
        } elseif (($unit == 'g' && $ingunit == 'kg') || ($unit == 'ml' && $ingunit == 'l')) {
            return $ingredient->total_quantity + ($totalQuantity / 1000);
        } elseif (($unit == 'kg' && $ingunit == 'g') || ($unit == 'l' && $ingunit == 'ml')) {
            return $ingredient->total_quantity + ($totalQuantity * 1000);
        }

        return $ingredient->total_quantity;
    }
    private function updateIngredientQuantity(Ingredient $ingredient, $newQuantity)
    {
        $ingredient->update(['total_quantity' => $newQuantity]);
    }
    public function calculateQuantity(EditQTYRequest $request, Ingredient $ingredient)
    {
        $unit = $request->unit;
        $ingunit = $ingredient->unit;
        $totalQuantity = $request->total_quantity;

        if ($unit == $ingunit) {
            return $ingredient->total_quantity - $totalQuantity;
        } elseif (($unit == 'g' && $ingunit == 'kg') || ($unit == 'ml' && $ingunit == 'l')) {
            return $ingredient->total_quantity - ($totalQuantity / 1000);
        } elseif (($unit == 'kg' && $ingunit == 'g') || ($unit == 'l' && $ingunit == 'ml')) {
            return $ingredient->total_quantity - ($totalQuantity * 1000);
        }

        return $ingredient->total_quantity;
    }
    public function storeDestruction($ingredient,$request)
    {
        if ($ingredient) {
            Destruction::create([
                'ingredient_id' => $ingredient->id,
                'qty' => $request->total_quantity,
                'unit' => $request->unit,
                'branch_id' => $ingredient->branch_id
            ]);
        }
    }
    public function addition($ingredient,$request) {
        Addition::create([
            'ingredient_id' => $ingredient->id,
            'qty' => $request->total_quantity,
            'unit' => $request->unit,
            'branch_id' => $ingredient->branch_id
        ]);
    }
    public function maxIng($ingredient)
    {
        if ($ingredient->total_quantity < 0) {
            $ingredient->total_quantity = max(0,$ingredient->total_quantity);
            $ingredient->save();
        }
    }
    public function editQty(Ingredient $ingredient, EditQTYRequest $request)
    {
        $request->validated($request->all());
        $newQuantity = $this->calculateNewQuantity($request, $ingredient);
        $this->updateIngredientQuantity($ingredient, $newQuantity);
        $this->addition($ingredient,$request);
        return $this->apiResponse(IngredientResource::make($ingredient), 'Quantity Updated', 200);
    }
    public function destruction(Ingredient $ingredient, EditQTYRequest $request)
    {
        $request->validated($request->all());
        $newQuantity = $this->calculateQuantity($request, $ingredient);
        $this->updateIngredientQuantity($ingredient, $newQuantity);
        $this->storeDestruction($ingredient,$request);
        $this->maxIng($ingredient);

        return $this->apiResponse(IngredientResource::make($ingredient),'Quantity Updated',200);

    }
    public function index(Branch $branch)
    {
        $add = Addition::where('branch_id',$branch->id)->get();
        return $this->apiResponse(AdditionResource::collection($add),'success',200); 
    }

    
}
