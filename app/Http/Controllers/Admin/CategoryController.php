<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    // public function index()
    // {
    //     $categories = CategoryResource::collection(Category::where('status',1)->orderByRaw('position IS NULL ASC, position ASC')->get());
    //     return $this->apiResponse($categories,'success',200);
    // }

    public function show(Category $category)
    {   
        if($category->status == 1) {
            return $this->apiResponse(CategoryResource::make($category),'success',200);
        }else{
            return $this->apiResponse(null,'Not Found',404);

        }
    }

    public function getCategories(Branch $branch)
    {
        $categories = $branch->category()->where('status',1)->orderByRaw('position IS NULL ASC, position ASC')->get();
        
        return $this->apiResponse(CategoryResource::collection($categories),'succcess',200);
    
    }
    // public function adminAll()
    // {
    //     $categories = CategoryResource::collection(Category::orderByRaw('position IS NULL ASC, position ASC')->get());
    //     return $this->apiResponse($categories,'success',200);
    // }
    public function adminShow(Category $category)
    {   
        return $this->apiResponse(CategoryResource::make($category),'success',200);
    }
    public function adminCategory(Branch $branch)
    {
        $categories = $branch->category()->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(CategoryResource::collection($categories),'succcess',200);

    }

    public function store(AddCategoryRequest $request , Category $category)
    {
        $request->validated($request->all());

        $category = Category::create($request->except('position'));
        if($request->position)
        {
            $categories = Category::where('branch_id',$request->branch_id)->orderBy('position','ASC')->get();
            if ($categories->isNotEmpty())
            {
                foreach($categories as $cat){
                    if($cat->position >= $request->position && $cat->position != null ){
                        $cat->position++;
                        $cat->save();
                    } 
                }
            }
            $category->position = $request->position; 
        }
        $category->save();
        $category->ReOrder($request);

        return $this->apiResponse(new CategoryResource($category),'Data successfully saved',201);
    }
    public function update(EditCategoryRequest $request , Category $category)
    {

        $request->validated($request->all());

        if($request->hasFile('image'))
        {
            File::delete(public_path($category->image));
        }
        $category->update($request->except('position'));
        if($request->position)
        {
            $categories = Category::where('branch_id',$request->branch_id)->orderBy('position','ASC')->get();

            foreach($categories as $cat){
                if($cat->position >= $request->position && $cat->position != null ){
                    $cat->position++;
                    $cat->save();
                } 
            }
            $category->position = $request->position; 
        }
        $category->save();

        $category->ReOrder($request);

        return $this->apiResponse(CategoryResource::make($category),'Data successfully saved',200);
    }

    public function delete(Category $category)
    {
        
        File::delete(public_path($category->image));

        $category->delete();
        $category->ReOrder($category);

        return $this->apiResponse(null,'Data successfully deleted',200);

    }
    public function changeStatus(Category $category)
    {
        if ($category->status == 1) {
            $category->status = 0;
            $category->save();
            return $this->apiResponse($category->status,'Status change successfully.',200);

        }
        $category->status = 1;
        $category->save();
        return $this->apiResponse($category,'Status change successfully.',200);
    }



}

