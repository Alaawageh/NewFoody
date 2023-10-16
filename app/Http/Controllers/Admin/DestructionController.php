<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Destruction;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Resources\DestructionResource;
use Illuminate\Http\Request;

class DestructionController extends Controller
{
    use ApiResponseTrait;
    public function index(Branch $branch)
    {
        $destruction = Destruction::where('branch_id',$branch->id)->get();
        return $this->apiResponse(DestructionResource::collection($destruction),'success',200); 
    }
}
