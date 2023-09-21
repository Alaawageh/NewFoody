<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddRestaurantRequest;
use App\Http\Requests\EditRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\User;
use App\Types\UserTypes;
use Symfony\Component\HttpFoundation\Response;

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
        // if ($restaurant->id !== auth()->user()->id) {
        //     return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN) ;

        // }
        return $this->apiResponse(RestaurantResource::make($restaurant), 'success', 200);
    }

    public function store(AddRestaurantRequest $request)
    {
        $request->validated($request->all());

        $restaurant = Restaurant::create(array_merge(
            $request->except('password'),
            ['password' => bcrypt($request->password)]
        ));
        if($restaurant)
        {
            $branch = Branch::create([
                'name' => $request->name,
                'address' => $request->address,
                'taxRate' => '15%',
                'restaurant_id' => $restaurant->id
            ]);
            User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type' => UserTypes::ADMIN,
                'branch_id' => $branch->id,
            ]);
        }

        return $this->apiResponse(new RestaurantResource($restaurant), 'Data Successfully Saved', 201);

    }
    public function update(EditRestaurantRequest $request ,Restaurant $restaurant)
    {
        $request->validated($request->all());

        $restaurant->update(array_merge(
            $request->except('password'),
            ['password' => bcrypt($request->password)]
        ));
        
        $branch = Branch::find($restaurant->id);
       $branch->update([
        'name' => $request->name,
        'address' => $request->address,
        'taxRate' => '15%',
        'restaurant_id' => $restaurant->id
       ]);
       $user = User::find($restaurant->id);
       $user->update([
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'user_type' => UserTypes::ADMIN,
        'branch_id' => $branch->id,
       ]);

        return $this->apiResponse(RestaurantResource::make($restaurant), 'Data Successfully Updated', 200);
    }
    
    public function delete(Restaurant $restaurant)
    {
        $restaurant->delete();
        $user = User::find($restaurant->id);
        $user->delete();
        $branch = Branch::find($restaurant->id);
        $branch->delete();
        
        return $this->apiResponse(null, 'Deleted Successfully', 200);
    }


}
