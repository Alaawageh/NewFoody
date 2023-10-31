<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AddProductRequest;
use App\Http\Requests\Product\EditProductRequest;
use App\Http\Requests\ProductExtraRequest;
use App\Http\Requests\ProductIngRequest;
use App\Http\Resources\IngredientResource;
use Illuminate\Http\Request;
use App\Http\Resources\ProductIngredientResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\RemoveIngredientResource;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ExtraIngredient;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\ProductIngredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponseTrait;
    public function show(Product $product)
    {
        if($product->status == 1) {
            return $this->apiResponse(ProductResource::make($product),'success',200);
        }else{
            return $this->apiResponse(null,'Not Found',200);
        }
    }

    public function getProducts(Category $category)
    {
        if($category->status == 1) {
            $products = $category->product()->where('status',1)->orderByRaw('position IS NULL ASC, position ASC')->get();
            return $this->apiResponse(ProductResource::collection($products),'success',200);
        }else{
            return $this->apiResponse(null,'Not Found',404);

        }

    }
    public function getproductByBranch(Branch $branch,Category $category)
    {
        if($category->status == 1){
            $products = $branch->product()->where('status',1)->orderByRaw('position IS NULL ASC, position ASC')->get();
            return $this->apiResponse(ProductResource::collection($products),'success',200);
        }

    }
    public function getAllbyBranch(Branch $branch)
    {
        $products = $branch->product()->where('status',1)->whereHas('category', function ($query) use ($branch){
            $query->where('status',1)->where('branch_id',$branch->id);
        })->orderByRaw('position IS NULL ASC, position ASC')->get();

        return $this->apiResponse(ProductResource::collection($products),'success',200);
    }
    public function getByCategory(Category $category)
    {
        $products = $category->product()->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(ProductResource::collection($products),'success',200);
    }
    public function position($request,$product)
    {
        if($request->position) {
            $products = Product::where('category_id',$request->category_id)->orderBy('position')->get();
            if ($products->isNotEmpty()) {
                foreach ($products as $pro) {
                    if($pro->position >= $request->position && $pro->position != null ){
                        $pro->position++;
                        $pro->save();
                    } 
                }
                $product->position = $request->position;
            }
        }
        $product->save();
    }

    public function store(AddProductRequest $request , Product $product)
    {
        $request->validated($request->all());

        $product = Product::create($request->except('position'));
        $this->position($request,$product);
        $product->ReOrder($request);


        return $this->apiResponse(new ProductResource($product),'Data Successfully Saved',201);

    }
    public function CheckHasFile($product)
    {
        File::delete(public_path($product->image));
    }
    
    public function update(EditProductRequest $request , Product $product)
    {
        $request->validated($request->all());
        if($request->hasFile('image')) {
            $this->CheckHasFile($product);
        }
        $MaxPosition = Product::where('branch_id', $product->branch_id)->where('category_id', $product->category_id)->max('position');
        $currentPosition = $product->position;
        $newPosition = $request->position;
        if ($newPosition > $MaxPosition + 1) {
            Product::where('category_id', $product->category_id)
            ->where('branch_id',$product->branch_id)
            ->where('position', '>', $currentPosition)
            ->where('position', '<=', $MaxPosition)
            ->where('position','!=',null)
            ->decrement('position');  
            $product->update(array_merge($request->except('position'),['position' => $MaxPosition]));
        }else{
            if ($newPosition > $currentPosition && $newPosition < $MaxPosition + 1) {
                Product::where('category_id', $product->category_id)
                        ->where('branch_id',$product->branch_id)
                        ->where('position', '>', $currentPosition)
                        ->where('position', '<=', $newPosition)
                        ->where('position','!=',null)
                        ->decrement('position'); 
                
            }elseif ($newPosition < $currentPosition && $newPosition < $MaxPosition + 1) {
                Product::where('category_id', $product->category_id)
                        ->where('branch_id', $product->branch_id)
                        ->where('position', '>=', $newPosition)
                        ->where('position', '<', $currentPosition)
                        ->where('position','!=',null)
                        ->increment('position');            
            }
            $product->update($request->all());
        }
       
        return $this->apiResponse(ProductResource::make($product),'Data Successfully Saved',200);
    }

    public function delete(Product $product)
    {
        $this->CheckHasFile($product);
        $product->delete();
        $product->ReOrder($product);
        return $this->apiResponse(null,'Data successfully Deleted',200);
    }

    public function changeStatus(Product $product)
    {
        $product->status == 1 ? $product->status = 0 : $product->status = 1;

        $product->save();

        return $this->apiResponse($product->status,'Status change successfully.',200);
    }

    public function getByBranch(Branch $branch)
    {
        $products = $branch->product()->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(ProductResource::collection($products),'success',200);
    }

    public function getRemoveIng()
    {
        $removed = ProductIngredient::with('product.branch','product.category')->where('is_remove', 1)->get();
        return $this->apiResponse(ProductIngredientResource::collection($removed),'success',200);
    }

    public function getRemoveByProduct(Product $product)
    {
        $remove = ProductIngredient::where('product_id',$product->id)->where('is_remove', 1)->get();
        return $this->apiResponse(RemoveIngredientResource::collection($remove),'success',200);
    }

    public function editIng(ProductIngRequest $request,Product $product)
    {
        $request->validated();
        if (is_array($request->ingredients)) {
            $ingredientIds = [];
            foreach ($request->ingredients as $ingredient) {
                $ingredientIds[$ingredient['id']] = [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                    'is_remove' => $ingredient['is_remove']
                ];
            }
            $product->ingredients()->syncWithoutDetaching($ingredientIds);
            return $this->apiResponse(ProductResource::make($product),'success',200);
        }    
    }
    public function editExtra(ProductExtraRequest $request,Product $product)
    {
        $request->validated();
        if (is_array($request->extra_ingredients)) {
            $ingredientIds = [];
            foreach ($request->extra_ingredients as $ingredient) {
                $extra = ExtraIngredient::find($ingredient['id']);
                $ingredientIds[$ingredient['id']] = [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                    'price_per_piece' => ($extra->price_per_kilo * $ingredient['quantity'])/1000
                ];
            }
            $product->extraIngredients()->syncWithoutDetaching($ingredientIds);
            return $this->apiResponse(ProductResource::make($product),'success',200);
        }    
    }

    public function deleteIng(Product $product,Ingredient $ingredient)
    {
        
        $product->ingredients()->detach($ingredient->id);
        return $this->apiResponse(ProductResource::make($product),'success',200); 
    }
    public function deleteExtra(Product $product,ExtraIngredient $extraIngredient)
    {
        
        $product->extraIngredients()->detach($extraIngredient->id);
        return $this->apiResponse(ProductResource::make($product),'success',200); 
    }
    public function editIsRemove($product_id, $ingredient_id)
    {
        $productIngredient = ProductIngredient::where('product_id', $product_id)
                                            ->where('ingredient_id', $ingredient_id)
                                            ->firstOrFail();

        $productIngredient->is_remove = !$productIngredient->is_remove;
        $productIngredient->save();

        return $this->apiResponse($productIngredient,'updated successfully',200);
    }
    public function getIngredients(Product $product)
    {
        $ing = $product->ingredients()->get();
        return $this->apiResponse(IngredientResource::collection($ing),'success',200);

    }
    public function Ingredients(Product $product)
    {
        $ing = $product->ingredients()->get();
        return $this->apiResponse($ing,'success',200);

    }

    public function searchProducts(Request $request,Branch $branch)
    {
        $products = Product::where('status',1)->where('branch_id',$branch->id)->whereHas('category',function ($q) {
            $q->where('status',1);
        })->where('name', 'LIKE', "%$request->name%")->get();
        return response()->json($products);
    }


}
