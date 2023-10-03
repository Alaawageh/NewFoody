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
use Symfony\Component\HttpFoundation\Response;


class BranchController extends Controller
{
    use ApiResponseTrait; 

    public function show(Branch $branch)
    {
        return $this->apiResponse(BranchResource::make($branch), 'success', 200);
    }
    
    public function getBranches(Restaurant $restaurant)
    {
        // if($restaurant->id === auth()->user()->id) {
            $branches = $restaurant->branch()->get();
            return $this->apiResponse(BranchResource::collection($branches),'success',200);
        // }
        // return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN) ;

    }

    public function store(AddBranchRequest $request)
    {
        $request->validated($request->all());

        $branch = Branch::create([
            'name' => $request->name,
            'address' => $request->address,
            'taxRate' => $request->taxRate,
            'restaurant_id' => $request->restaurant_id
        ]);
 
        return $this->apiResponse(new BranchResource($branch),'Data successfully saved',201);
    }

    public function update(EditBranchRequest $request ,Branch $branch)
    {
        $request->validated($request->all()); 

        $branch->update([
            'name' => $request->name,
            'address' => $request->address,
            'taxRate' => $request->taxRate,
            'restaurant_id' => $request->restaurant_id
        ]);

        return $this->apiResponse(BranchResource::make($branch),'Data successfully saved',200);

    }

    public function delete(Branch $branch)
    {

        $branch->delete();
        
        return $this->apiResponse(null, 'Deleted Successfully', 200);
    }

    public function getTax(Branch $branch)
    {
        if($branch) {
            $tax = (intval($branch->taxRate) / 100);
            return response()->json(['TaxRate' => $tax] , 200);

        }
    }
}
