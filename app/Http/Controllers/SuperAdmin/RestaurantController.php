<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddRestaurantRequest;
use App\Http\Requests\EditRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $restaurants = RestaurantResource::collection(Restaurant::all());
        return $this->apiResponse($restaurants, 'success', 200);
    }

    public function show(Restaurant $restaurant)
    {
        return $this->apiResponse(RestaurantResource::make($restaurant), 'success', 200);
    }

    public function store(AddRestaurantRequest $request)
    {
        $request->validated($request->all());

        $restaurant = Restaurant::create($request->all());

        return $this->apiResponse(new RestaurantResource($restaurant), 'Data Successfully Saved', 201);

    }
    public function update(EditRestaurantRequest $request ,Restaurant $restaurant)
    {
        $request->validated($request->all());

        $restaurant->update($request->all());

        return $this->apiResponse(RestaurantResource::make($restaurant), 'Data Successfully Updated', 200);
    }
    
    public function delete(Restaurant $restaurant)
    {
        $restaurant->delete();
        
        return $this->apiResponse(null, 'Deleted Successfully', 200);
    }


}
