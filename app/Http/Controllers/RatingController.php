<?php

namespace App\Http\Controllers;

use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use ApiResponseTrait;

    public function add(Request $request)
    {
        $ratings = Rating::create($request->all());
        return $this->apiResponse(new RatingResource($ratings),'Done',201);
    }
    public function index()
    {
        $ratings = RatingResource::collection(Rating::with('product')->get());
        return $this->apiResponse(new RatingResource($ratings),'Done',201);
    }
}
