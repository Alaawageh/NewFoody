<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    use ApiResponseTrait;

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

    public function adminShow(Category $category)
    {   
        return $this->apiResponse(CategoryResource::make($category),'success',200);
    }
    public function adminCategory(Branch $branch)
    {
        $categories = $branch->category()->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(CategoryResource::collection($categories),'succcess',200);
    }
    public function position(Request $request)
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
    }
    public function store(AddCategoryRequest $request , Category $category)
    {
        $request->validated($request->all());

        $category = Category::create($request->except('position'));
        if($request->position)
        {
            $this->position($request);
            $category->position = $request->position; 
        }
        $category->save();
        $category->ReOrder($request);

        return $this->apiResponse(new CategoryResource($category),'Data successfully saved',201);
    }
    public function CheckHasFile($category)
    {
        File::delete(public_path($category->image));
    }
    public function update(EditCategoryRequest $request , Category $category)
    {
        $request->validated($request->all());
        if($request->hasFile('image'))
        {
            $this->CheckHasFile($category);
        }
        $category->update($request->except('position'));
        if($request->position)
        {
            $this->position($request);
            $category->position = $request->position; 
        }
        $category->save();
        $category->ReOrder($request);

        return $this->apiResponse(CategoryResource::make($category),'Data successfully saved',200);
    }

    public function delete(Category $category)
    {
        $this->CheckHasFile($category);
        $category->delete();
        $category->ReOrder($category);
        return $this->apiResponse(null,'Data successfully deleted',200);
    }
    public function changeStatus(Category $category)
    {
        $this->changeCategoryStatus($category);
        return $this->apiResponse($this->getCategoryStatus($category), 'Status change successfully.', 200);
    }
    private function changeCategoryStatus(Category $category)
    {
        $category->status = $category->status == 1 ? 0 : 1;
        $category->save();
    }
    private function getCategoryStatus(Category $category)
    {
        return $category->status == 1 ?  $category : $category->status;
    }
}

