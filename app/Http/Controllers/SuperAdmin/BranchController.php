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

    // public function index()
    // {
    //     $branch = BranchResource::collection(Branch::get());
    //     return $this->apiResponse($branch,'success',200);
    // }

    public function show(Branch $branch)
    {
        if ($branch->restaurant_id !== auth()->user()->id) {
            return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN) ;

        }
        return $this->apiResponse(BranchResource::make($branch), 'success', 200);
    }
    
    public function getBranches(Restaurant $restaurant)
    {
        if ($restaurant->id !== auth()->user()->id) {
            return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN) ;

        }
        $branches = $restaurant->branch()->get();
        return $this->apiResponse(BranchResource::collection($branches),'success',200);
    }

    public function store(AddBranchRequest $request)
    {
        $request->validated($request->all());

        $restaurant = Restaurant::where('id', auth()->user()->id)->first();
        if($restaurant) {
            $branch = Branch::create([
                'name' => $request->name,
                'address' => $request->address,
                'taxRate' => $request->taxRate,
                'restaurant_id' => $restaurant->id
            ]);
        }
        return $this->apiResponse(new BranchResource($branch),'Data successfully saved',201);
    }

    public function update(EditBranchRequest $request ,Branch $branch)
    {
        $request->validated($request->all()); 

        if ($branch->restaurant_id !== auth()->user()->id) {
            return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN) ;

        }
        $restaurant = Restaurant::where('id', auth()->user()->id)->first();
        $branch->update([
            'name' => $request->name,
            'address' => $request->address,
            'taxRate' => $request->taxRate,
            'restaurant_id' => $restaurant->id
        ]);

        return $this->apiResponse(BranchResource::make($branch),'Data successfully saved',200);

    }

    public function delete(Branch $branch)
    {
        if ($branch->restaurant_id !== auth()->user()->id) {
            return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN) ;

        }
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
