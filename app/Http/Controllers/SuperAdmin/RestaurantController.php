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
        
       $branch = Branch::where('restaurant_id',$restaurant->id)->first();
       $branch->update($request->only('name'));

       $user = User::where('branch_id',$branch->id)->first();
       $user->update(array_merge($request->only(['email','password'])),['password' => bcrypt($request->password)]);
        
       return $this->apiResponse(RestaurantResource::make($restaurant), 'Data Successfully Updated', 200);
    }
    
    public function delete(Restaurant $restaurant)
    {
        $branches = $restaurant->branch()->get();
        foreach($branches as $branch) {
            
            $branch->users()->delete();
            $branch->delete();
        }
        
        $restaurant->delete();
        
        return $this->apiResponse(null, 'Deleted Successfully', 200);
    }


}
