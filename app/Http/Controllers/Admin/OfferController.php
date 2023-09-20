<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddOfferRequest;
use App\Http\Requests\EditOfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Branch;
use App\Models\Offer;
use Illuminate\Support\Facades\File;

class OfferController extends Controller
{
    use ApiResponseTrait;

    // public function index()
    // {
    //     $offers = OfferResource::collection(Offer::get());
    //     return $this->apiResponse($offers,'success',200);
    // }

    public function getOffers(Branch $branch)
    {
        $offers = $branch->offer()->get();
        return $this->apiResponse(OfferResource::collection($offers),'success',200);
    }

    public function show(Offer $offer)
    {
        return $this->apiResponse(OfferResource::make($offer),'success',200);
    }

    public function store(AddOfferRequest $request)
    {
        $request->validated($request->all()); 

        $offer = Offer::create($request->all());

        return $this->apiResponse(new OfferResource($offer),'The offer Saved',201);
    }

    public function update(EditOfferRequest $request , Offer $offer)
    {
        $request->validated($request->all());
        if( $request->hasFile('image'))
        {
            File::delete(public_path($offer->image));
        }
        $offer->update($request->all());
        return $this->apiResponse(OfferResource::make($offer), 'Data Successfully Updated', 200);
    }

    public function delete(Offer $offer)
    {
        File::delete(public_path($offer->image));

        $offer->delete();

        return $this->apiResponse(null,'The offer deleted',200);
    }
}
